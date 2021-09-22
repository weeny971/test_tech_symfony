<?php

namespace App\Repository;

use App\Entity\Newsletter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Newsletter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Newsletter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Newsletter[]    findAll()
 * @method Newsletter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsletterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Newsletter::class);
    }

    public function findByUser($value)
    {
        return $this->getEntityManager()
             ->createQuery('SELECT DISTINCT(n) 
                            FROM App:Newsletter n 
                            LEFT JOIN App:User u 
                            WITH n.id IN (u.news) 
                            WHERE u.id = :id')
             ->setParameter('id', $value)
             ->getResult();

        /*$qb  = $this->createQueryBuilder();
        $qb->select("n")
            ->from('App:Newsletter','n')
            //->leftJoin('u.news', 'x')
            ->innerJoin('n', 'App:User', 'u', 'u.id = p.user_id')
            ->andWhere('u.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
            ;
        return $qb;*/
    }

    // /**
    //  * @return Newsletter[] Returns an array of Newsletter objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Newsletter
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
