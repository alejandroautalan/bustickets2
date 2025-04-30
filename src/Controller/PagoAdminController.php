<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Pago;
use App\Entity\Pasaje;
use Doctrine\Persistence\ManagerRegistry;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PagoAdminController extends CRUDController
{

    private $entityManager;
    private $adminPool;

    public function __construct(EntityManagerInterface $entityManager, Pool $adminPool)
    {
        $this->entityManager = $entityManager;
        $this->adminPool = $adminPool;
    }

    #[Route('/admin/procesar-asientos', name: 'admin_procesar_asientos', methods: ['POST'])]
    public function asientosAction(Request $request, ManagerRegistry $doctrine): Response
    {

        $em         = $this->entityManager;
        $data       = json_decode($request->getContent(), true);
        $total      = 0;
        $pago       = new Pago();
        $this->admin->setSubject($pago);
        foreach ($data as $d):
            $pasaje = $em->getRepository(Pasaje::class)->find($d['pid']);
            $total = $total + $pasaje->getCosto();
            $pago->addPasaje($pasaje);
        endforeach;
        $modelManager = $this->admin->getModelManager();
        $form = $this->createFormBuilder($pago)
            ->add('monto', null, ['data'=>(string)$total])
            ->add('fecha', DatePickerType::class, ['format'=>'d/M/y'])
            ->add('tipo', ChoiceType::class,
                ['choices' => [
                    'Transferencia' => 1,
                    'Efectivo' => 2,
                ], 'label' => 'Tipo'])
            ->add('importeRecibido')
            ->add('numeroComprobante')
            ->add('pasajes', CollectionType::class, ['by_reference' => false,
                'label' => 'Pasaje',
                #'disabled' => $disabled,
                'required'   => true,
            ],
                [
                    'inline'       => 'standard',
                    'edit'         => 'standard',
                    'sortable'     => 'position'
                ])
            ->setAction($this->generateUrl('admin_app_pago_procesar'))
            ->getForm();

        // Si la solicitud es AJAX, renderiza solo el formulario
        if ($request->isXmlHttpRequest()) {
            return $this->render('ViajeAdmin/modal.html.twig', [
                'form' => $form,
                'object' => $pago,
                'admin' => $this->admin,
                'objectId' => null,
                'sonata_admin' => $this->admin,
            ]);
        }

        // Si no es una solicitud AJAX, puedes renderizar una página completa
        return $this->render('ViajeAdmin/modal.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/procesar-formulario', name: 'admin_procesar_formulario', methods: ['POST'])]
    public function procesarAction(Request $request): Response
    {
        $em = $this->entityManager;
        $admin = $this->container->get('sonata.admin.pool')->getAdminByAdminCode('admin.pago');

        if (!$admin) {
            throw $this->createNotFoundException('Admin not found');
        }

        $object = $admin->getNewInstance();
        $admin->setSubject($object);
        $form = $admin->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            foreach ($formData->getPasajes() as $p):
                $p->setEstado(1);
                $em->persist($p);
            endforeach;
            $em->persist($object);
            $em->flush();
            $this->addFlash('success', 'Asientos reservados con exito.');

            return $this->redirectToRoute('admin_app_viaje_asientos');
        }
        $this->addFlash('success', 'Asientos reservados con exito.');

        return $this->redirectToRoute('admin_app_viaje_list');
    }

    public function setPasajeAction(Request $request): Response
    {
        $em        = $this->entityManager;
        $idpasaje  = $request->get('idpasaje');
        $idviaje   = $request->get('idviaje');
        $pasaje    = $em->getRepository(Pasaje::class)->find($idpasaje);
        if($pasaje):
            $pasaje->setEstado(2);
            $em->persist($pasaje);
            $em->flush();
            $this->addFlash('success', 'Asientos asignado con exito.');
            return $this->redirectToRoute('admin_app_viaje_ocuparAsiento', ['id' => $idviaje]);
        else:
            $this->addFlash('error', 'Asiento no asignado.');
            return $this->redirectToRoute('admin_app_viaje_ocuparAsiento', ['id' => $idviaje]);
        endif;
    }

    public function ocuparAsientoAction(Request $request): Response
    {
        $em        = $this->entityManager;
        $r         = $request->getContent();
        $data      = json_decode($r);
        $pasaje    = $em->getRepository(Pasaje::class)->find($data->idpasaje);
        $url        = $this->generateUrl(
            'admin_app_pago_setPasaje',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $html = '<form action="'.$url.'" method="post" enctype="multipart/form-data">
                <div class="box box-primary">
                <input type="hidden" name="idpasaje" value="'.$data->idpasaje.'" />
                <input type="hidden" name="idviaje" value="'.$data->idviaje.'" />
                <table class="table">
                    <tr>
                        <td>Asiento</td>
                        <td>'.$pasaje->getAsientoColectivo().'</td>
                    </tr>
                    <tr>
                        <td>Apellido y Nombre</td>
                        <td>'.$pasaje->getPasajero()->getApellido().', '.$pasaje->getPasajero()->getNombre().'</td>
                    </tr>
                    <tr>
                        <td>Dni</td>
                        <td>'.$pasaje->getPasajero()->getDni().'</td>
                    </tr>
                 </table>
                    <div   class="sonata-ba-form-actions well well-small form-actions">
                        <button type="submit" class="btn btn-primary">Asignar</button>
                    </div>
                 </form>
                 ';

        return new JsonResponse($html);
    }
}
