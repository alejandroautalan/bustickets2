<?php

namespace App\Repository;

use App\Entity\Transporte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;

/**
 * @extends ServiceEntityRepository<Transporte>
 */
class TransporteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transporte::class);
    }

    static public function createGridPositionCriteria($planta, $row, $col) {
        return Criteria::create()
        ->andWhere(Criteria::expr()->eq('planta', $planta))
        ->andWhere(Criteria::expr()->eq('row', $row))
        ->andWhere(Criteria::expr()->eq('col', $col))
        ;
    }

    //    /**
    //     * @return Transporte[] Returns an array of Transporte objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Transporte
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
