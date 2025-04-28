<?php

namespace App\Repository;

use App\Entity\Trayecto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\TrayectoParada;


/**
 * @extends ServiceEntityRepository<Trayecto>
 */
class TrayectoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trayecto::class);
    }

    public function getOrigen(Trayecto $t)
    {
        $parada_repo = $this->getEntityManager()->getRepository(TrayectoParada::class);
        return $parada_repo->createQueryBuilder('p')
            ->andWhere('p.trayecto = :trayecto')
            ->andWhere('p.es_origen = TRUE')
            ->setParameter('trayecto', $t)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getDestino(Trayecto $t)
    {
        $parada_repo = $this->getEntityManager()->getRepository(TrayectoParada::class);
        return $parada_repo->createQueryBuilder('p')
            ->andWhere('p.trayecto = :trayecto')
            ->andWhere('p.es_destino = TRUE')
            ->setParameter('trayecto', $t)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return Trayecto[] Returns an array of Trayecto objects
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

    //    public function findOneBySomeField($value): ?Trayecto
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
