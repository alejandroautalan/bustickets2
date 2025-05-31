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

class MercadoPagoWebhookController extends AbstractController
{

    #[Route('/webhook/mercadopago', name: 'mercadopago_webhook', methods: ['POST'])]
    public function webhook(Request $request, EntityManagerInterface $entityManager): Response
    {
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
