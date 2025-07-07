<?php
namespace App\Command;

use App\Entity\Boleto;
use App\Entity\Reserva;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ActualizarBoletoEstados extends Command
{
    protected static $defaultName = 'app:actualizar-boletos';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setDescription('Actualiza los estados de los boletos no pagados en cierto tiempo.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fechaLimite = new \DateTime('-5 minutes');
        $repository = $this->entityManager->getRepository(Boleto::class);
        $boletos = $repository->createQueryBuilder('b')
            #->innerJoin('b.reserva', 'r')
            ->where('b.estado = :espera')
            #->andWhere('r.estado = :draft')
            ->andWhere('b.update_at < :limite')
            ->setParameter('espera', Boleto::STATE_RESERVED_WAIT)
            #->setParameter('draft', Reserva::STATE_PENDING_PAYMENT)
            ->setParameter('limite', $fechaLimite->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult();
        $b=0;
        foreach ($boletos as $boleto) {
            $boleto->setEstado(Boleto::STATE_DRAFT);
            #$reserva = $boleto->getReserva();
            #$reserva->setEstado(Reserva::STATE_DRAFT);
            $this->entityManager->persist($boleto);
            #$this->entityManager->persist($reserva);
            $b++;
        }

        $this->entityManager->flush();

        $output->writeln('fecha hora. ('.$fechaLimite->format('Y-m-d H:i:s').')');
        $output->writeln('Boletos actualizados. ('.$b.')');

        return 0;
    }
}