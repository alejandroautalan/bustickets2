<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Controller\CRUDController;


final class ReservaAdminController extends CRUDController
{
    protected function redirectTo(Request $request, object $object): RedirectResponse
    {
        if (null !== $request->get('btn_finalize')) {
            $route = 'show';
            $url = $this->admin->generateObjectUrl(
                $route,
                $object,
                $this->getSelectedTab($request)
            );

            return new RedirectResponse($url);
        }

        return parent::redirectTo($request, $object);
    }

}
