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

use App\Form\Model\Registro;
use App\Form\Type\RegistroType;


class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function requestLoginLink(NotifierInterface $notifier, LoginLinkHandlerInterface $loginLinkHandler, UserRepository $userRepository, Request $request): Response
    {
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
            $notification = new LoginLinkNotification(
                $loginLinkDetails,
                'Welcome to MY WEBSITE!' // email subject
            );
            // create a recipient for this user
            $recipient = new Recipient($user->getEmail());

            // send the notification to the user
            $notifier->send($notification, $recipient);

            // render a "Login link is sent!" page
            return $this->render('security/login_link_sent.html.twig');
        }

        // if it's not submitted, render the form to request the "login link"
        return $this->render('security/request_login_link.html.twig');
    }

    #[Route('/register', name: 'register')]
    public function registerAndSendLoginLink(
        NotifierInterface $notifier,
        LoginLinkHandlerInterface $loginLinkHandler,
        UserRepository $userRepository,
        Request $request,
        UserManagerInterface $userManager
    ): Response
    {
        $email = $request->get('email');
        $registro = new Registro();
        $registro->setEmail($email);
        $form = $this->createForm(RegistroType::class, $registro);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $registro = $form->getData();
            $username = $registro->getEmail();
            $email = $registro->getEmail();
            $password = bin2hex(random_bytes(64 / 2));  # 32 caracteres

            $user = $userManager->create();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPlainPassword($password);
            $user->setEnabled(true);
            $user->setSuperAdmin(false);

            $userManager->save($user);

            return $this->redirectToRoute('task_success');
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
