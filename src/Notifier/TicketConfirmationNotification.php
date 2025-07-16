<?php
// src/Notifier/TicketConfirmationNotification.php

namespace App\Notifier;

use App\Repository\UserRepository;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

// Para usar objetos Address para 'from' y 'to' más flexibles

class TicketConfirmationNotification extends Notification implements EmailNotificationInterface
{
    private $buyerName;
    private $buyerIdNumber;
    private $tripData;
    private $ticketData;
    private $paymentInfo;
    private $buyerEmail;

    public function __construct(
        string $buyerName,
        string $buyerIdNumber,
        array $tripData,
        array $ticketData,
        array $paymentInfo,
        string $buyerEmail,
    ) {
        parent::__construct('¡Tu Pasaje ha sido Confirmado!'); // Título de la notificación
        $this->buyerName = $buyerName;
        $this->buyerIdNumber = $buyerIdNumber;
        $this->tripData = $tripData;
        $this->ticketData = $ticketData;
        $this->paymentInfo = $paymentInfo;
        $this->buyerEmail = $buyerEmail;
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        // Puedes definir el remitente de forma más robusta, ej. desde parameters.yaml
        $senderEmail = $_ENV['MAILER_SENDER'];
        $senderName = 'El SantiagueñoBus';

        // El destinatario se obtiene del Recipient que se pasa al NotifierInterface::send()

        $email = (new TemplatedEmail())
            ->from(new Address($senderEmail, $senderName))
            ->to(new Address($this->buyerEmail, $this->buyerName))
            ->subject($this->getSubject()) // Usa el título definido en el constructor
            ->htmlTemplate('email/ticket_confirmation.html.twig')
            ->context([
                'buyer_name' => $this->buyerName,
                'buyer_id_number' => $this->buyerIdNumber,
                'trip' => $this->tripData,
                'ticket' => $this->ticketData,
                'payment' => $this->paymentInfo,
            ]);

        return new EmailMessage($email);
    }
}