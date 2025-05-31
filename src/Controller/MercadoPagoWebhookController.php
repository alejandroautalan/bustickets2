<?php

namespace App\Controller;

use App\Entity\Reserva;
use Doctrine\ORM\EntityManagerInterface;
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
    public function webhook(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Obtain the x-signature value from the header
        $xSignature = $request->headers->get('HTTP_X_SIGNATURE');
        $xRequestId = $request->headers->get('HTTP_X_REQUEST_ID');

        // Obtain Query params related to the request URL
        $queryParams = $_GET;

        // Extract the "data.id" from the query params
        $dataID = isset($queryParams['data.id']) ? $queryParams['data.id'] : '';

        // Separating the x-signature into parts
        $parts = explode(',', $xSignature);

        // Initializing variables to store ts and hash
        $ts = null;
        $hash = null;

        // Iterate over the values to obtain ts and v1
        foreach ($parts as $part) {
            // Split each part into key and value
            $keyValue = explode('=', $part, 2);
            if (count($keyValue) == 2) {
                $key = trim($keyValue[0]);
                $value = trim($keyValue[1]);
                if ($key === "ts") {
                    $ts = $value;
                } elseif ($key === "v1") {
                    $hash = $value;
                }
            }
        }

        // Obtain the secret key for the user/application from Mercadopago developers site
        $secret = $this->getParameter('WEBHOOK_SECRET');

        // Generate the manifest string
        $manifest = "id:$dataID;request-id:$xRequestId;ts:$ts;";

        // Create an HMAC signature defining the hash type and the key as a byte array
        $sha = hash_hmac('sha256', $manifest, $secret);
        if ($sha === $hash) {
            // HMAC verification passed
            echo "HMAC verification passed";
        } else {
            // HMAC verification failed
            echo "HMAC verification failed";
        }
        if (isset($data['type']) && $data['type'] === 'payment') {
            $paymentId = $data['data']['id'];

            #MercadoPagoConfig::setAccessToken('APP_USR-7745628252612000-050318-f7578701336f67a894934818b76bc06f-2418800269'); // Obtén esto de tus credenciales de Mercado Pago

            #$client = new PaymentClient();
            #$payment = $client->get($paymentId);


            // Ahora tienes el objeto de pago completo para trabajar
            // $payment->status;
            // $payment->transaction_amount;
            // etc.

            $reserva = $entityManager->getRepository(Reserva::class)->findBy(['payment_id' => $paymentId]);
            $reserva->setEstado(Reserva::STATE_COMPLETED);
            $entityManager->persist($reserva);
            $entityManager->flush();

        }
        return new Response('Webhook recibido y procesado', Response::HTTP_OK);
    }
}
