<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\UserBundle\Model\UserManagerInterface;
use App\Repository\PasajeroRepository;
use App\Form\Model\Registro;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getPasajeroForUser($user, PasajeroRepository $pasajeroRepository)
    {
        $pasajero = $pasajeroRepository->findOneByDni($user->getDni());
        return $pasajero;
    }

    public function registerFinalUser(
        Registro $registro,
        UserManagerInterface $userManager,
        EntityManagerInterface $entityManager,
        PasajeroRepository $pasajeroRepository)
    {
        $username = $registro->getEmail();
        $email = $registro->getEmail();
        $password = bin2hex(random_bytes(64 / 2));  # 32 caracteres

        $user = $userManager->create();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled(true);
        $user->setSuperAdmin(false);
        $user->setDni($registro->getNroDocumento());
        $roles = [
            "ROLE_FINAL_USER",
            "ROLE_ADMIN_SERVICIO_LIST",
            "ROLE_ADMIN_SERVICIO_VIEW",
            "ROLE_ADMIN_RESERVA_CREATE",
            "ROLE_ADMIN_RESERVA_EDIT",
        ];
        foreach($roles as $role) {
            $user->addRole($role);
        }
        $userManager->save($user);

        $pasajero = $pasajeroRepository->findOneByDni($registro->getNroDocumento());
        if(null == $pasajero) {
            $pasajero = new Pasajero();
            $pasajero->setNombre($registro->getNombre())
                ->setApellido($registro->getApellido())
                ->setDni($registro->getNroDocumento())
                ->setSexo($registro->getSexo())
            ;
            $entityManager->persist($pasajero);
            $entityManager->flush();
        }
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
