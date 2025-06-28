<?php

namespace App\EventListener;

use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;


class LoginSuccessListener
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(LoginSuccessEvent $event): void
    {
        $firewall = $event->getFirewallName();
        if("main" != $firewall) {
            return;
        }

        # Process logins from main firewall
        $servicioList = 'admin_app_servicio_list';
        $url = $this->urlGenerator->generate($servicioList);
        $event->setResponse(new RedirectResponse($url));
    }
}
