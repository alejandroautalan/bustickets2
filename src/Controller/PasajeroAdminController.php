<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Pasajero;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class PasajeroAdminController extends CRUDController
{

    public function searchForDniAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data1      = $request->getContent();
        $data       = json_decode($data1);
        $dni = $data->dni;
        $pasajero = $entityManager->getRepository(Pasajero::class)->findOneBy(['dni' => $dni]);
        $data=null;
        if($pasajero):
            $data['apellido'] = $pasajero->getApellido();
            $data['nombre']   = $pasajero->getNombre();
            $data['sexo']     = $pasajero->getSexo();
        endif;
        return new JsonResponse($data);
    }
}
