<?php

namespace App\Controller;

use App\Entity\Reserva;
use Doctrine\ORM\EntityManagerInterface;
use MercadoPago\Client\MercadoPagoClient;
use MercadoPago\Client\Preference\PreferenceClient;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use Symfony\Component\Webhook\Client\RequestParser;
use Symfony\Component\HttpFoundation\Request;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class MercadoPagoWebhookController extends AbstractController
{

    #[Route('/webhook/mercadopago', name: 'mercadopago_webhook', methods: ['POST'])]
    public function webhook(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        // 1. Obtener los valores de los headers X-Signature y X-Request-Id
        // `getRequestUri()` devuelve el valor del header, null si no existe.
        $xSignature = $request->headers->get('X-Signature');
        $xRequestId = $request->headers->get('X-Request-Id');

        // Validar que los headers existen
        if (null === $xSignature || null === $xRequestId) {
            $logger->warning('Webhook de Mercado Pago recibido sin X-Signature o X-Request-Id.', [
                'xSignature' => $xSignature,
                'xRequestId' => $xRequestId,
            ]);
            return new Response('Headers faltantes.', Response::HTTP_BAD_REQUEST);
        }

        $dataId = $request->query->get('data.id', '');
        $notificationType = $request->query->get('type', '');
        // 3. Separar la x-signature en partes
        $parts = explode(',', $xSignature);

        // Inicializando variables para almacenar ts y hash
        $ts = null;
        $hash = null;

        // Iterar sobre los valores para obtener ts y v1
        foreach ($parts as $part) {
            // Dividir cada parte en clave y valor
            $keyValue = explode('=', $part, 2);
            if (count($keyValue) == 2) {
                $key = trim($keyValue[0]);
                $value = trim($keyValue[1]);
                if ($key === "ts") {
                    $ts = $value;
                } elseif ($key === "v1") { // Suponemos que v1 es el hash que buscamos
                    $hash = $value;
                }
            }
        }

        // Validar que ts y hash fueron extraídos
        if (null === $ts || null === $hash) {
            $logger->warning('X-Signature de Mercado Pago malformado.', [
                'xSignature' => $xSignature,
                'parts' => $parts,
            ]);
            return new Response('X-Signature malformado.', Response::HTTP_BAD_REQUEST);
        }

        // 4. Obtener la clave secreta
        $secret = $_ENV['WEBHOOK_SECRET'];

        if (null === $secret || empty($secret)) {
            $logger->error('La clave secreta de Mercado Pago no está configurada.');
            return new Response('Error interno del servidor: clave secreta no configurada.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // 5. Generar la cadena manifest
        $manifest = "id:$dataId;request-id:$xRequestId;ts:$ts;";

        // 6. Crear una firma HMAC definiendo el tipo de hash y la clave como un array de bytes
        $sha = hash_hmac('sha256', $manifest, $secret);

        if ($sha === $hash) {
            // Verificación HMAC aprobada
            $logger->info('Verificación HMAC de Mercado Pago exitosa para data.id: ' . $dataId);
            $accesst = $_ENV['ENV_ACCESS_TOKEN'];
            MercadoPagoConfig::setAccessToken($accesst);
            $logger->warning('Datos del manifest.', [
                'data_id' => $dataId,
                'x_request_id' => $xRequestId,
                'ts' => $ts,
                'expected_hash' => $sha,
                'received_hash' => $hash,
                'manifest' => $manifest,
            ]);
            switch($notificationType) {
                case "payment":
                    $payment = PreferenceClient::find_by_id($dataId);
                    break;
            #    case "plan":
            #        $plan = MercadoPagoClient::find_by_id($dataId);
            #        break;
            #    case "subscription":
            #        $subscription = MercadoPagoClient::find_by_id($dataId);
            #        break;
            #    case "invoice":
            #        $invoice = MercadoPagoClient::find_by_id($dataId);
            #        break;
            #    case "point_integration_wh":
            #        // $_POST contiene la informaciòn relacionada a la notificaciòn.
            #        break;
            }
            $logger->info('Payment: ' . json_encode($payment));
            $reserva = $entityManager->getRepository(Reserva::class)->findBy(['payment_id' => $payment->id]);
            $reserva->setEstado(Reserva::STATE_COMPLETED);
            $entityManager->persist($reserva);
            $entityManager->flush();
            return new Response('Webhook procesado con éxito.', Response::HTTP_OK);
        } else {
            // Verificación HMAC fallida
            $logger->warning('Fallo en la verificación HMAC de Mercado Pago.', [
                'data_id' => $dataId,
                'x_request_id' => $xRequestId,
                'ts' => $ts,
                'expected_hash' => $sha,
                'received_hash' => $hash,
                'manifest' => $manifest,
            ]);
            return new Response('Fallo en la verificación de firma.', Response::HTTP_UNAUTHORIZED);
        }
    }

    #[Route('/mercadopago/return', name: 'mercadopago_return', methods: ['GET'])]
    public function returnUrl(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        // Loggear todos los parámetros recibidos para depuración
        $queryParams = $request->query->all();
        $logger->info('Mercado Pago Return URL recibida.', $queryParams);

        // Obtener los parámetros relevantes
        $collectionId = $request->query->get('collection_id');
        $collectionStatus = $request->query->get('collection_status');
        $paymentId = $request->query->get('payment_id'); // A menudo es lo mismo que collection_id
        $status = $request->query->get('status'); // Estado general
        $externalReference = $request->query->get('external_reference'); // Tu referencia externa si la enviaste
        $preferenceId = $request->query->get('preference_id');
        ##actulizo payment_id####
        $reserva = $entityManager->getRepository(Reserva::class)->findBy(['preference_id' => $preferenceId]);
        $reserva->setPaymentId($paymentId);
        $entityManager->persist($reserva);
        $entityManager->flush();
        // Puedes agregar más parámetros según tus necesidades, como:
        $paymentType = $request->query->get('payment_type');
        $merchantOrderId = $request->query->get('merchant_order_id');
        $siteId = $request->query->get('site_id');
        $processingMode = $request->query->get('processing_mode');
        $merchantAccountId = $request->query->get('merchant_account_id');

        // Lógica de tu aplicación basada en el estado del pago
        $message = '';
        switch ($collectionStatus) {
            case 'approved':
                $message = '¡Tu pago ha sido aprobado! ID de la transacción: ' . $paymentId;
                // Aquí podrías redirigir a una página de "Gracias por tu compra"
                // O iniciar alguna lógica de actualización si no tienes webhooks confiables
                break;
            case 'rejected':
                $message = 'Tu pago fue rechazado. Intenta de nuevo o prueba con otro medio de pago.';
                // Aquí podrías redirigir a una página de "Pago rechazado"
                break;
            case 'pending':
                $message = 'Tu pago está pendiente. Esperando confirmación.';
                // Aquí podrías redirigir a una página de "Pago pendiente"
                break;
            default:
                $message = 'Estado de pago desconocido o no especificado.';
                break;
        }

        $logger->info('Lógica de retorno de Mercado Pago ejecutada.', [
            'collection_status' => $collectionStatus,
            'external_reference' => $externalReference,
            'message_to_user' => $message,
        ]);

        return new Response(
            sprintf(
                '<html><body><h1>Estado de tu pago</h1><p>%s</p><p>ID de la colección: %s</p><p>Referencia Externa: %s</p><p>Puedes regresar a la página principal haciendo clic <a href="/">aquí</a>.</p></body></html>',
                $message,
                $collectionId ?? 'N/A', // Usar el operador null coalescing para valores que podrían ser nulos
                $externalReference ?? 'N/A'
            )
        );
    }
}
