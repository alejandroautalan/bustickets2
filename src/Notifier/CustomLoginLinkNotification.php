<?php

namespace App\Notifier;


use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;


class CustomLoginLinkNotification extends LoginLinkNotification
{
    protected LoginLinkDetails $link_details;

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $emailMessage = parent::asEmailMessage($recipient, $transport);

        // get the NotificationEmail object and override the template
        $email = $emailMessage->getMessage();
        $email->content($this->getDefaultContentEs("el botón de abajo"))
              ->action('Ingresar', $this->link_details->getUrl());
        #$email->htmlTemplate('emails/custom_login_link_email.html.twig');

        return $emailMessage;
    }

    public function setLoginLinkDetails(LoginLinkDetails $link_details) {
        $this->link_details = $link_details;
    }

    private function getDefaultContentEs(string $target): string
    {
        $duration = $this->link_details->getExpiresAt()->getTimestamp() - time();
        $durationString = floor($duration / 60).' minuto'.($duration > 60 ? 's' : '');
        if (($hours = $duration / 3600) >= 1) {
            $durationString = floor($hours).' hora'.($hours >= 2 ? 's' : '');
        }

        $msg = 'Haz click en %s para confirmar que quieres iniciar sesión. Este enlace caducará en %s.';
        return sprintf($msg, $target, $durationString);
    }
}
