<?php

namespace App\Repository;

use App\Entity\Reserva;
use App\Entity\Boleto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reserva>
 */
class ReservaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reserva::class);
    }

    public function get_asientos_libres($servicio_id) {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT ta.id
            FROM App\Entity\Servicio s
            JOIN s.transporte t
            JOIN t.asientos ta
            LEFT JOIN App\Entity\Boleto b WITH b.asiento = ta.id AND b.servicio = s.id
            WHERE s = :servicio_id
              AND b.asiento is NULL
              OR (b.estado = :boleto_draft
                AND b.asiento not in (
                    SELECT IDENTITY(bb.asiento) FROM App\Entity\Boleto bb
                    where bb.servicio = :servicio_id
                    and bb.estado in (:boleto_reservado, :boleto_reservado_taken)
                    )
                )
            '
        )->setParameter('servicio_id', $servicio_id)
        ->setParameter('boleto_draft', Boleto::STATE_DRAFT)
        ->setParameter('boleto_reservado', Boleto::STATE_RESERVED)
        ->setParameter('boleto_reservado_taken', Boleto::STATE_RESERVED_TAKEN)
        ;
        $rs = [];
        foreach($query->getResult() as $row) {
            $rs[] = $row['id'];
        }
        return $rs;
    }

    public function get_asientos_reservados($servicio_id) {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT ta.id
            FROM App\Entity\Servicio s
            JOIN s.transporte t
            JOIN t.asientos ta
            LEFT JOIN App\Entity\Boleto b WITH b.asiento = ta.id AND b.servicio = s.id
            WHERE s = :servicio_id
            AND  (b.asiento is not NULL and b.estado = :boleto_reservado)'
        )->setParameter('servicio_id', $servicio_id)
        ->setParameter('boleto_reservado', Boleto::STATE_RESERVED)
        ;
        $rs = [];
        foreach($query->getResult() as $row) {
            $rs[] = $row['id'];
        }
        return $rs;
    }

    public function get_asientos_reserva($reserva_id) {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT IDENTITY(b.asiento) as id
            FROM App\Entity\Boleto b
            WHERE b.reserva = :reserva_id'
        )->setParameter('reserva_id', $reserva_id);
        $rs = [];
        foreach($query->getResult() as $row) {
            $rs[] = $row['id'];
        }
        return $rs;
    }

    //    /**
    //     * @return Reserva[] Returns an array of Reserva objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reserva
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
