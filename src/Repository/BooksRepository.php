<?php

namespace App\Repository;

use App\Entity\Books;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Books|null find($id, $lockMode = null, $lockVersion = null)
 * @method Books|null findOneBy(array $criteria, array $orderBy = null)
 * @method Books[]    findAll()
 * @method Books[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BooksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Books::class);
    }

     // pas de précision de l'entité pcq je suis déjà dedans
     public function findBestBooks($limit)
     {
         return $this->createQueryBuilder('a')
                 ->select('a as fiche, AVG(c.rating) as avgRatings')
                 ->join('a.comments','c')
                 ->groupBy('a')
                 ->orderBy('avgRatings','DESC')
                 ->setMaxResults($limit)
                 ->getQuery()
                 ->getResult()
         ;
     }


     public function findByFilter($filter, $offset, $limit)
     {
         return $this->createQueryBuilder('b')
                    ->join('b.genres', 'g')
                    ->where('g.name = :val')
                    ->setParameter('val', $filter)
                    ->setFirstResult($offset)
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult()
                ;
     }

     public function mySearch($search)
     {
         return $this->createQueryBuilder('b')
                    ->where('b.title LIKE :search OR b.author LIKE :search')
                    ->setParameter('search', '%'.$search.'%')
                    ->getQuery()
                    ->execute()
                ;
     }
     
    // /**
    //  * @return Books[] Returns an array of Books objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Books
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
