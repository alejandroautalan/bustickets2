<?php

namespace App\Repository;

use App\Entity\Reserva;
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
            LEFT JOIN App\Entity\Boleto b WITH b.asiento = ta.id
            WHERE s = :servicio_id
            AND  (b.asiento is NULL or b.estado = 0)'
        )->setParameter('servicio_id', $servicio_id);
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
            LEFT JOIN App\Entity\Boleto b WITH b.asiento = ta.id
            WHERE s = :servicio_id
            AND  (b.asiento is not NULL and b.estado = 1)'
        )->setParameter('servicio_id', $servicio_id);
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
