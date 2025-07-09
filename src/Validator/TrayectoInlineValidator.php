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
        $has_origen = false;
        $has_destino = false;
        $msg_hora = 'Debe ingresar hora.';

        foreach($lista_paradas as $tp) {
            $parada = $tp->getParada();

            if(null == $parada) {
                // El punto debe tener un valor
                $msg = 'Seleccione parada' . $idx . ' ord ' . $tp->getNroOrden();
                $errorElement->with('trayectoParadas['.$idx.'].parada')->addViolation($msg)->end();
            }

            $es_primer_punto = $tp->getNroOrden() == 1;
            $es_punto_origen = $tp->getTipoParadaId() == Trayecto::TIPO_PARADA_ORIGEN;
            $es_punto_destino = $tp->getTipoParadaId() == Trayecto::TIPO_PARADA_DESTINO;
            $es_ultimo_punto = $tp->getNroOrden() == $total;
            $es_punto_intermedio = (!$es_primer_punto and !$es_ultimo_punto);

            if($es_primer_punto) {
                $has_origen = true;
                if(!$es_punto_origen) {
                    # primer punto debe ser origen
                    $msg = 'El primer punto debe ser de tipo "%s".';
                    $msg = sprintf($msg, Trayecto::$tipos_parada[Trayecto::TIPO_PARADA_ORIGEN]);
                    $errorElement->with('trayectoParadas['.$idx.'].tipo_parada_id')->addViolation($msg)->end();
                }
                $partida = $tp->getHoraPartida();
                if($partida == null) {
                    $msg = $msg_hora;
                    $errorElement->with('trayectoParadas['.$idx.'].hora_partida')->addViolation($msg)->end();
                }
            }
            if($es_ultimo_punto) {
                $has_destino = true;
                if(!$es_punto_destino) {
                    # el ultio punto debe ser destino
                    $msg = 'El último punto debe ser de tipo "%s".';
                    $msg = sprintf($msg, Trayecto::$tipos_parada[Trayecto::TIPO_PARADA_DESTINO]);
                    $errorElement->with('trayectoParadas['.$idx.'].tipo_parada_id')->addViolation($msg)->end();
                }
                $llegada = $tp->getHoraLlegada();
                if($llegada == null) {
                    $msg = $msg_hora;
                    $errorElement->with('trayectoParadas['.$idx.'].hora_llegada')->addViolation($msg)->end();
                }
            }
            if($es_punto_intermedio) {
                $llegada = $tp->getHoraLlegada();
                $partida = $tp->getHoraPartida();
                if($llegada == null) {
                    $msg = $msg_hora;
                    $errorElement->with('trayectoParadas['.$idx.'].hora_llegada')->addViolation($msg)->end();
                }
                if($partida == null and $es_punto_origen) {
                    $msg = $msg_hora;
                    $errorElement->with('trayectoParadas['.$idx.'].hora_partida')->addViolation($msg)->end();
                }
            }

            $idx += 1;
        }

        if(!$has_origen) {
            $msg = 'Debe especificar un punto de origen.';
            $errorElement->with('trayectoParadas')->addViolation($msg)->end();
        }
        if(!$has_destino) {
            $msg = 'Debe especificar un punto de destino.';
            $errorElement->with('trayectoParadas')->addViolation($msg)->end();
        }

        // Updata origen y destino en trayecto.
        $tp_origen = $lista_paradas->first();  # retorna false si no hay
        $tp_destino = $lista_paradas->last();  # retorna false si no hay
        if($tp_origen and $tp_destino) {
            $trayecto->setOrigen($tp_origen->getParada());
            $trayecto->setDestino($tp_destino->getParada());
        }
    }
}
