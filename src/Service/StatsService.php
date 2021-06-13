<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StatsService{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getUsersCount(){
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getBooksCount()
    {
        return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Books a')->getSingleScalarResult();
    }


    public function getCommentsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
    }

    public function getBooksStats($direction)
    {
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName
            FROM App\Entity\Comment c
            JOIN c.book a
            JOIN a.utilisateur u
            GROUP BY a
            ORDER BY note '. $direction
        )
        ->setMaxResults(5)
        ->getResult();
    }

} 