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
        $secret = $_ENV['WEBHOOK_SECRET'];

        // Generate the manifest string
        $manifest = "id:$dataID;request-id:$xRequestId;ts:$ts;";
        $logger->info($manifest);
        // Create an HMAC signature defining the hash type and the key as a byte array
        $sha = hash_hmac('sha256', $manifest, $secret);
        $logger->info($sha);
        if ($sha === $hash) {
            // HMAC verification passed
            MercadoPagoConfig::setAccessToken("ENV_ACCESS_TOKEN");
            switch($_POST["type"]) {
                case "payment":
                    $payment = MercadoPagoClient::find_by_id($_POST["data"]["id"]);
                    break;
                case "plan":
                    $plan = MercadoPagoClient::find_by_id($_POST["data"]["id"]);
                    break;
                case "subscription":
                    $subscription = MercadoPagoClient::find_by_id($_POST["data"]["id"]);
                    break;
                case "invoice":
                    $invoice = MercadoPagoClient::find_by_id($_POST["data"]["id"]);
                    break;
                case "point_integration_wh":
                    // $_POST contiene la informaciòn relacionada a la notificaciòn.
                    break;
            }
            $logger->info(json_encode($payment));
            $reserva = $entityManager->getRepository(Reserva::class)->findBy(['payment_id' => $payment->id]);
            $reserva->setEstado(Reserva::STATE_COMPLETED);
            $entityManager->persist($reserva);
            $entityManager->flush();

            return new Response('Webhook recibido y procesado', Response::HTTP_OK);
        } else {
            // HMAC verification failed
            return new Response('HMAC verification failed', Response::HTTP_UNAUTHORIZED);
        }

    }
}
