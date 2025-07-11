<?php

namespace App\Repository;

use App\Entity\ConfigPrecio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConfigPrecio>
 */
class ConfigPrecioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfigPrecio::class);
    }

    public function getCosto($origen_parada, $destino_parada)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT cp.costo as costo, (
                ISNULL(IDENTITY(cp.origen_parada)) + ISNULL(IDENTITY(cp.destino_parada)) +
                ISNULL(IDENTITY(cp.origen_ciudad)) + ISNULL(IDENTITY(cp.destino_ciudad))
            ) as peso
            FROM App\Entity\ConfigPrecio cp
            WHERE cp.origen_provincia = :origen_provincia_id
            AND cp.destino_provincia = :destino_provincia_id

            AND (
                (cp.origen_parada = :origen_parada_id)
                or
                (cp.origen_parada is NULL and cp.origen_ciudad = :origen_ciudad_id)
                or
                (cp.origen_parada is NULL AND cp.origen_ciudad is NULL)
                )
            AND (
                (cp.destino_parada = :destino_parada_id)
                or
                (cp.destino_parada is NULL and cp.destino_ciudad = :destino_ciudad_id)
                or
                (cp.destino_parada is NULL AND cp.destino_ciudad is NULL)
                )
            ORDER BY peso')
        ->setParameter('origen_provincia_id', $origen_parada->getProvincia()->getId())
        ->setParameter('origen_ciudad_id', $origen_parada->getCiudad()->getId())
        ->setParameter('origen_parada_id', $origen_parada->getId())
        ->setParameter('destino_provincia_id', $destino_parada->getProvincia()->getId())
        ->setParameter('destino_ciudad_id', $destino_parada->getCiudad()->getId())
        ->setParameter('destino_parada_id', $destino_parada->getId())
        ->setMaxResults(1)
        ;
        $rs = $query->getResult();

        return $rs[0]['costo'];
    }

    public function existeConfiguracion(ConfigPrecio $config) {
        # Se utiliza en inline validator, $config puede no tener ID

        $entityManager = $this->getEntityManager();
        $qb = $this->createQueryBuilder('cp');
        $qb->select('count(cp.id)')
            ->andWhere('cp.origen_provincia = :origen_provincia_id')
            ->andWhere('cp.destino_provincia = :destino_provincia_id')
            ->andWhere('cp.categoria_id = :categoria_id')
            ->setParameter('origen_provincia_id', $config->getOrigenProvincia())
            ->setParameter('destino_provincia_id', $config->getDestinoProvincia())
            ->setParameter('categoria_id', $config->getCategoriaId())
            ;
        $config_id = $config->getId();
        $ociudad = $config->getOrigenCiudad();
        $oparada = $config->getOrigenParada();
        $dciudad = $config->getDestinoCiudad();
        $dparada = $config->getDestinoParada();

        if(null !== $config_id) {
            $qb->andWhere('cp.id != :config_id')
                ->setParameter('config_id', $config_id);
        }
        if(null !== $ociudad) {
            $qb->andWhere('cp.origen_ciudad = :origen_ciudad_id')
                ->setParameter('origen_ciudad_id', $ociudad)
            ;
        } else {
            $qb->andWhere('cp.origen_ciudad IS NULL');
        }
        if(null !== $oparada) {
            $qb->andWhere('cp.origen_parada = :origen_parada_id')
            ->setParameter('origen_parada_id', $oparada)
            ;
        } else {
            $qb->andWhere('cp.origen_parada IS NULL');
        }

        if(null !== $dciudad) {
            $qb->andWhere('cp.destino_ciudad = :destino_ciudad_id')
            ->setParameter('destino_ciudad_id', $dciudad)
            ;
        } else {
            $qb->andWhere('cp.destino_ciudad IS NULL');
        }
        if(null !== $dparada) {
            $qb->andWhere('cp.destino_parada = :destino_parada_id')
            ->setParameter('destino_parada_id', $dparada)
            ;
        } else {
            $qb->andWhere('cp.destino_parada IS NULL');
        }

        // $query = $entityManager->createQuery(
        //     'SELECT count(cp.id)
        //     FROM App\Entity\ConfigPrecio cp
        //     WHERE cp.origen_provincia = :origen_provincia_id
        //     and (cp.origen_ciudad = :origen_ciudad_id OR cp.origen_ciudad IS NULL)
        //     and (cp.origen_parada = :origen_parada_id OR cp.origen_parada IS NULL)
        //     and cp.destino_provincia = :destino_provincia_id
        //     and (cp.destino_ciudad = :destino_ciudad_id OR cp.destino_ciudad IS NULL)
        //     and (cp.destino_parada = :destino_parada_id or cp.destino_parada IS NULL)
        //     and cp.categoria_id = :categoria_id
        //     '
        // )->setParameter('origen_provincia_id', $config->getOrigenProvincia())
        // ->setParameter('origen_ciudad_id', $config->getOrigenCiudad())
        // ->setParameter('origen_parada_id', $config->getOrigenParada())
        // ->setParameter('destino_provincia_id', $config->getDestinoProvincia())
        // ->setParameter('destino_ciudad_id', $config->getDestinoCiudad())
        // ->setParameter('destino_parada_id', $config->getDestinoParada())
        // ->setParameter('categoria_id', $config->getCategoriaId())
        // ;

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    //    /**
    //     * @return ConfigPrecio[] Returns an array of ConfigPrecio objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ConfigPrecio
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
