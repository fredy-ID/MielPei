<?php

namespace App\Repository;

use App\Entity\ProducerRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProducerRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProducerRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProducerRequest[]    findAll()
 * @method ProducerRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProducerRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProducerRequest::class);
    }

    // /**
    //  * @return ProducerRequest[] Returns an array of ProducerRequest objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProducerRequest
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
