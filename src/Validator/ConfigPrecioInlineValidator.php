<?php
declare(strict_types=1);

namespace App\Validator;

use Sonata\Form\Validator\ErrorElement;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\ConfigPrecio;


class ConfigPrecioInlineValidator
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate(ErrorElement $errorElement, ConfigPrecio $config)
    {
        $repo = $this->entityManager->getRepository(ConfigPrecio::class);
        $existe_config = $repo->existeConfiguracion($config);
        if($existe_config) {
            $msg = 'Ya existe esta configuración.';
            $errorElement->addViolation($msg)->end();
        }
    }
}
