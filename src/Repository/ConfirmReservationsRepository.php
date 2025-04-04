<?php

namespace App\Repository;

use App\Entity\ConfirmReservations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConfirmReservations>
 *
 * @method ConfirmReservations|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfirmReservations|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfirmReservations[]    findAll()
 * @method ConfirmReservations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfirmReservationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfirmReservations::class);
    }

    public function add(ConfirmReservations $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ConfirmReservations $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ConfirmReservations[] Returns an array of ConfirmReservations objects
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

//    public function findOneBySomeField($value): ?ConfirmReservations
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
