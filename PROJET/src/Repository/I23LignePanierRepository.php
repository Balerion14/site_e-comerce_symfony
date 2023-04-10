<?php

namespace App\Repository;

use App\Entity\I23LignePanier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<I23LignePanier>
 *
 * @method I23LignePanier|null find($id, $lockMode = null, $lockVersion = null)
 * @method I23LignePanier|null findOneBy(array $criteria, array $orderBy = null)
 * @method I23LignePanier[]    findAll()
 * @method I23LignePanier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class I23LignePanierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, I23LignePanier::class);
    }

    public function save(I23LignePanier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(I23LignePanier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return I23LignePanier[] Returns an array of I23LignePanier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?I23LignePanier
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
