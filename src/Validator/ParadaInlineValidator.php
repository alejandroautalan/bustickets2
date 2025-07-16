<?php
declare(strict_types=1);

namespace App\Validator;

use Sonata\Form\Validator\ErrorElement;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Trayecto;
use App\Entity\Parada;


class ParadaInlineValidator
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate(ErrorElement $errorElement, Parada $parada)
    {
        $repo = $this->entityManager->getRepository(Parada::class);
        $existe_parada = $repo->existeParada($parada);
        if($existe_parada) {
            $msg = 'Esta parada ya existe.';
            $errorElement->addViolation($msg)->end();
        }
    }
}
