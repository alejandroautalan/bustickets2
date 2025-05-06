<?php
declare(strict_types=1);

namespace App\Validator;

use Sonata\Form\Validator\ErrorElement;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Reserva;
use App\Entity\Pago;


class ReservaInlineValidator
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate(ErrorElement $errorElement, Reserva $reserva)
    {
        # Validar pasajero seleccionado
        $idx = 0;
        foreach($reserva->getBoletos() as $boleto) {
            $pasajero = $boleto->getPasajero();

            if(null == $pasajero) {
                $msg = 'Seleccione pasajero';
                $errorElement->with('boletos['.$idx.'].pasajero')->addViolation($msg)->end();
            }
            $idx += 1;
        }

        # Validar que un pasajero solo ocupe un asiento en el servicio actual
        # TODO

        # Validar Pago
        if(Reserva::STATE_PENDING_PAYMENT == $reserva->getEstado()) {
            $idx = 0;
            foreach($reserva->getPagos() as $pago) {
                $pago_indefinido = $pago->getTipo() == Pago::PAYMENT_TYPE_UNSPECIFIED;
                if($pago_indefinido) {
                    $msg = 'Seleccione Tipo de Pago';
                    $errorElement->with('pagos['.$idx.'].tipo')->addViolation($msg)->end();
                }

                $total = $pago->getMonto();
                $recibido = $pago->getImporteRecibido();
                if($recibido < $total) {
                    $msg = 'Importe recibido debe ser mayor o igual al total.';
                    $errorElement->with('pagos['.$idx.'].importeRecibido')->addViolation($msg)->end();
                }

                $idx += 1;
            }

        }
    }
}
