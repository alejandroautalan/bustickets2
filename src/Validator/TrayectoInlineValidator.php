<?php
declare(strict_types=1);

namespace App\Validator;

use Sonata\Form\Validator\ErrorElement;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Trayecto;


class TrayectoInlineValidator
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate(ErrorElement $errorElement, Trayecto $trayecto)
    {
        # Validar paradas seleccionadas
        $idx = 0;
        $lista_paradas = $trayecto->getTrayectoParadas();
        $total = $lista_paradas->count();

        foreach($lista_paradas as $tp) {
            $parada = $tp->getParada();

            if(null == $parada) {
                // El punto debe tener un valor
                $msg = 'Seleccione parada';
                $errorElement->with('trayectoParadas['.$idx.'].parada')->addViolation($msg)->end();
            }
            else {
                $es_primer_punto = $tp->getNroOrden() == 1;
                $es_punto_origen = $tp->getTipoParadaId() == Trayecto::TIPO_PARADA_ORIGEN;
                $es_punto_destino = $tp->getTipoParadaId() == Trayecto::TIPO_PARADA_DESTINO;
                $es_ultimo_punto = $tp->getNroOrden() == $total;

                if($es_primer_punto and !$es_punto_origen) {
                    # primer punto debe ser origen
                    $msg = 'El primer punto debe ser de tipo "%s".';
                    $msg = sprintf($msg, Trayecto::$tipos_parada[Trayecto::TIPO_PARADA_ORIGEN]);
                    $errorElement->with('trayectoParadas['.$idx.'].tipo_parada_id')->addViolation($msg)->end();
                }

                if($es_ultimo_punto and !$es_punto_destino) {
                    # el ultio punto debe ser destino
                    $msg = 'El último punto debe ser de tipo "%s".';
                    $msg = sprintf($msg, Trayecto::$tipos_parada[Trayecto::TIPO_PARADA_DESTINO]);
                    $errorElement->with('trayectoParadas['.$idx.'].tipo_parada_id')->addViolation($msg)->end();
                }
            }
            $idx += 1;
        }

        // Updata origen y destino en trayecto.
        $tp_origen = $lista_paradas->first();
        $tp_destino = $lista_paradas->last();
        if($tp_origen !== null and $tp_destino !== null) {
            $trayecto->setOrigen($tp_origen->getParada());
            $trayecto->setDestino($tp_destino->getParada());
        }
    }
}
