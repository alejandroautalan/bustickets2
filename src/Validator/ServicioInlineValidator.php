<?php
declare(strict_types=1);

namespace App\Validator;

use Sonata\Form\Validator\ErrorElement;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Trayecto;
use App\Entity\Servicio;
use App\Entity\ConfigPrecio;


class ServicioInlineValidator
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate(ErrorElement $errorElement, Servicio $servicio)
    {
        # Validar existencia de precios si el estado cambia a STATE_PROGRAMMED
        $status_changed_to_programmed = false;
        $servicio_id = $servicio->getId();
        $state_current = $servicio->getEstado();
        $is_new = (null == $servicio_id);

        if($is_new) {
            $status_changed_to_programmed = ($state_current == Servicio::STATE_PROGRAMMED);
        }
        else
        {
            $repo = $this->entityManager->getRepository(Servicio::class);
            $servicio_from_db = $repo->find($servicio->getId());
            $prev_state = $servicio_from_db->getEstado();
            $status_changed_to_programmed = ((
                $prev_state != Servicio::STATE_PROGRAMMED
                and $state_current == Servicio::STATE_PROGRAMMED)
                or (
                $prev_state == Servicio::STATE_PROGRAMMED
                and $state_current == Servicio::STATE_PROGRAMMED)
            );
        }

        $missing_config = [];
        if($status_changed_to_programmed) {
            $cp_repo = $this->entityManager->getRepository(ConfigPrecio::class);
            # Validar Precios origen - destino
            $trayecto = $servicio->getTrayecto();
            foreach($trayecto->getParadasOrigen() as $tp_origen) {
                foreach($trayecto->getParadasByNroOrden() as $tp_destino) {
                    $origen_pos = $tp_origen->getNroOrden();
                    $destino_pos = $tp_destino->getNroOrden();
                    # Nota: se verificara solo de paradas origenen->destino
                    # y no (por ahora) de origen->origen, es decir,
                    # Comprar y bajan en otra parada origen.
                    $destino_tipo = $tp_destino->getTipoParadaId();
                    $do_price_check = (
                        ($destino_pos > $origen_pos)
                        and ($destino_tipo == Trayecto::TIPO_PARADA_DESTINO)
                    );
                    if($do_price_check) {
                        $porigen = $tp_origen->getParada();
                        $pdestino = $tp_destino->getParada();
                        $costo = $cp_repo->getCosto($porigen, $pdestino);
                        if(null == $costo) {
                            $missing_config[] = sprintf('[%s > %s]', $porigen->getNombre(), $pdestino->getNombre());
                        }
                    }
                }
            }
        }
        if(!empty($missing_config)) {
            $msg = 'Se requiere configuración de costos para: ';
            $msg = $msg . implode(", ", $missing_config);
            $errorElement->addViolation($msg)->end();
        }

    }
}
