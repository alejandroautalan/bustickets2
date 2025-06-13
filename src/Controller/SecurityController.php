<?php

declare(strict_types=1);

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

use Sonata\UserBundle\Model\UserManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Form\Model\Registro;
use App\Form\Type\RegistroType;
use App\Notifier\CustomLoginLinkNotification;
use App\Repository\PasajeroRepository;
use App\Entity\Pasajero;


class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function requestLoginLink(NotifierInterface $notifier, LoginLinkHandlerInterface $loginLinkHandler, UserRepository $userRepository, Request $request): Response
    {
        $email = $request->get("email", "");

        // check if form is submitted
        if ($request->isMethod('POST')) {
            // load the user in some way (e.g. using the form input)
            $email = $request->getPayload()->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            if(null == $user) {
                return $this->redirectToRoute('register', ['email' => $email]);
            }

            // create a login link for $user this returns an instance
            // of LoginLinkDetails
            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
            $loginLink = $loginLinkDetails->getUrl();

            // create a notification based on the login link details
            // $notification = new LoginLinkNotification(
            $notification = new CustomLoginLinkNotification(
                $loginLinkDetails,
                'Bienvenido a Bustickets!' // email subject
            );
            $notification->setLoginLinkDetails($loginLinkDetails);
            // create a recipient for this user
            $recipient = new Recipient($user->getEmail());

            // send the notification to the user
            $notifier->send($notification, $recipient);

            // render a "Login link is sent!" page
            return $this->redirectToRoute('login_link_sent');
        }

        // if it's not submitted, render the form to request the "login link"
        $context = ["email" => $email];
        return $this->render('security/request_login_link.html.twig', $context);
    }

    #[Route('/login-link-sent', name: 'login_link_sent')]
    public function showLoginLinkSent(Request $request): Response
    {
        return $this->render('security/login_link_sent.html.twig');
    }

    #[Route('/register', name: 'register')]
    public function registerAndSendLoginLink(
        NotifierInterface $notifier,
        LoginLinkHandlerInterface $loginLinkHandler,
        UserRepository $userRepository,
        Request $request,
        UserManagerInterface $userManager,
        EntityManagerInterface $entityManager,
        PasajeroRepository $pasajeroRepository,
    ): Response
    {
        $email = $request->get('email');
        $registro = new Registro();
        $registro->setEmail($email);
        $form = $this->createForm(RegistroType::class, $registro);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $registro = $form->getData();

            $userRepository->registerFinalUser($registro, $userManager, $entityManager, $pasajeroRepository);

            return $this->redirectToRoute('login', ["email" => $email]);
        }

        $context = [
            'form' => $form,
        ];

        return $this->render('security/register_withlink.html.twig', $context);
    }

    #[Route('/login_check', name: 'login_check')]
    public function check(Request $request): Response
    {
        // get the login link query parameters
        $expires = $request->query->get('expires');
        $username = $request->query->get('user');
        $hash = $request->query->get('hash');

        // and render a template with the button
        return $this->render('security/process_login_link.html.twig', [
            'expires' => $expires,
            'user' => $username,
            'hash' => $hash,
        ]);
    }

}
