<?php

declare(strict_types=1);

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Entity\Reserva;
use Sonata\AdminBundle\Admin\Pool;
use App\Form\Type\OcuparType;
use App\Form\Type\AsientoSelectorType;
use Sonata\Form\Type\CollectionType;
// use App\Repository\TrayectoRepository;
// use App\Model\Reserva;
// use App\Form\Type\ReservaType;


final class ServicioAdminController extends CRUDController
{

    public function reservaAction(
        EntityManagerInterface $entityManager
    ): RedirectResponse
    {
        $servicio = $this->admin->getSubject();
        $trayecto = $servicio->getTrayecto();
        $reserva = new Reserva();
        $reserva->setServicio($servicio);
        $reserva->setOrigen($trayecto->getOrigen());
        $reserva->setDestino($trayecto->getDestino());

        $entityManager->persist($reserva);
        $entityManager->flush();

        return $this->redirectToRoute('admin_app_reserva_edit', ['id' => $reserva->getId()]);
    }


    public function asientosAction(
        EntityManagerInterface $entityManager
    ): Response
    {
        $servicio = $this->admin->getSubject();
        $reserva_repo = $entityManager->getRepository(Reserva::class);
        $asientos_reserva = $reserva_repo->get_asientos_reservados($servicio->getId());
        $reserva = new Reserva();
        $reserva->setOrigen($servicio->getTrayecto()->getOrigen());
        $reserva->setDestino($servicio->getTrayecto()->getDestino());
        $reserva->setServicio($servicio);
        $modelManager = $this->admin->getModelManager();
        $form = $this->createFormBuilder($reserva)
        ->add('origen', null, ['disabled' => true,])
        ->add('destino', null, ['disabled' => true,])
        ->add('servicio', null, ['disabled' => true,])
        ->add('asientos', OcuparType::class, [
            'label' => 'Ocupar Asientos',
            'transporte' => $servicio->getTransporte(),
            'asientos_libres' => $asientos_reserva,
            'required' => false,
            'mapped' => false])
            #->setAction($this->generateUrl('admin_app_servicio_procesar'))
            ->getForm();
        ;    
        #$reserva  = 
        return $this->render('ServicioAdmin/ocuparAsiento.html.twig', [
            'controller_name' => 'ServicioAdminController',
            'form' => $form,
            #'transporte' => $servicio->getTransporte(),
        ]);
    }

    public function archivoAction(
        EntityManagerInterface $entityManager
    ): Response
    {
        $data[] =
            ['apellido', 'nombre', 'tipo_documento', 'descripcion_documento', 'numero_documento', 'sexo', 'menor', 'nacionalidad', 'tripulante', 'ocupa_butaca'];
        ;

        $servicio = $this->admin->getSubject();
        foreach($servicio->getBoletos() as $boleto):
            $data[]=[$boleto->getPasajero()->getApellido(),$boleto->getPasajero()->getNombre(),'DNI', '', $boleto->getPasajero()->getDni(), $boleto->getPasajero()->getSexo(), '', 'AR', '', ''];
        endforeach;    
        
        
        
        // Crea una instancia de StreamedResponse para generar el archivo CSV de manera eficiente
        $response = new StreamedResponse();
        
        // Define el tipo de contenido y los encabezados para indicar que se trata de un archivo CSV
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="archivo.csv"');
        
        // Crea una función que escriba los datos CSV
        $response->setCallback(function () use ($data) {
            $handle = fopen('php://output', 'w');
        
            // Escribe los encabezados de la tabla
            fputcsv($handle, $data[0]);
        
            // Escribe los datos de la tabla
            for ($i = 1; $i < count($data); $i++) {
                fputcsv($handle, $data[$i]);
            }
        
            fclose($handle);
        });
        
        // Envía la respuesta al navegador
        return $response;
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

    /*
    public function reservaOldAction(TrayectoRepository $trayecto_repo)
    {
        $servicio = $this->admin->getSubject();
        $trayecto = $servicio->getTrayecto();
        $origen = $trayecto_repo->getOrigen($trayecto);
        $destino = $trayecto_repo->getDestino($trayecto);
        $reserva = new Reserva();
        $reserva->setOrigen($origen->getParada());
        $reserva->setDestino($destino->getParada());
        #print_r($reserva->getDestino()->getId()); die;
        $field_description = $this->admin->createFieldDescription('pasajero', [
            'translation_domain' => $this->admin->getTranslationDomain(),
            #'edit' => 'list',
        ]);
        $field_description->setAssociationAdmin($this->admin);
        $form = $this->createForm(
            ReservaType::class,
            $reserva,
            ['pasajero_model_manager' => $this->admin->getModelManager(),
             'pasajero_field_description' => $field_description]);

        return $this->render('ServicioAdmin/reservar.html.twig', [
            'controller_name' => 'ServicioAdminController',
            'object' => $servicio,
            'objectId' => $servicio->getId(),
            'action' => 'reserva',
            'servicio' => $servicio,
            'form' => $form,
        ]);
    }
    */

}
