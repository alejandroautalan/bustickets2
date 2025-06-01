<?php

namespace App\Controller;

use App\Entity\Reserva;
use Doctrine\ORM\EntityManagerInterface;
use MercadoPago\Client\MercadoPagoClient;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        // 2. Obtener los Query params relacionados con la URL de la solicitud
        $content = $request->getContent();
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['data']['id'])) {
            $logger->warning('Webhook de Mercado Pago con cuerpo JSON inválido o sin data.id.', [
                'content' => $content,
                'json_error' => json_last_error_msg(),
            ]);
            return new Response('Cuerpo de solicitud inválido o sin data.id.', Response::HTTP_BAD_REQUEST);
        }

        $dataId = $data['data']['id'];
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
        // Se recomienda definir esta clave en .env (o .env.local)
        // Ejemplo: MERCADOPAGO_SECRET_KEY="tu_clave_secreta_aqui"
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
            switch($_POST["type"]) {
                case "payment":
                    $payment = MercadoPagoClient::find_by_id($dataId);
                    break;
                case "plan":
                    $plan = MercadoPagoClient::find_by_id($dataId);
                    break;
                case "subscription":
                    $subscription = MercadoPagoClient::find_by_id($dataId);
                    break;
                case "invoice":
                    $invoice = MercadoPagoClient::find_by_id($dataId);
                    break;
                case "point_integration_wh":
                    // $_POST contiene la informaciòn relacionada a la notificaciòn.
                    break;
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
}
