<?php

namespace App\Repository;

use App\Entity\Parada;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Parada>
 */
class ParadaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parada::class);
    }

    public function existeParada(Parada $parada) {
        # Se utiliza en inline validator, $parada puede no tener ID

        $entityManager = $this->getEntityManager();
        $qb = $this->createQueryBuilder('p');
        $qb->select('count(p.id)')
            ->andWhere('p.nombre = :nombre')
            ->andWhere('p.provincia = :provincia_id')
            ->andWhere('p.ciudad = :ciudad_id')
            ->setParameter('provincia_id', $parada->getProvincia())
            ->setParameter('ciudad_id', $parada->getCiudad())
            ->setParameter('nombre', $parada->getNombre())
            ;
        $parada_id = $parada->getId();
        if(null !== $parada_id) {
            $qb->andWhere('p.id != :parada_id')
                ->setParameter('parada_id', $parada_id);
        }

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    //    /**
    //     * @return Parada[] Returns an array of Parada objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Parada
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
