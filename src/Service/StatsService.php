<?php

namespace App\Service;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Config\Definition\Exception\Exception;


class StatsService
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function getUsersCount()
    {
        return $this->manager->createQuery(' SELECT COUNT(u) FROM App\Entity\User u ')->getSingleScalarResult();
    }

    public function getAdsCount()
    {
        return $this->manager->createQuery(' SELECT COUNT(a) FROM App\Entity\Ad a ')->getSingleScalarResult();
    }

    public function getBookingsCount()
    {
        return $this->manager->createQuery(' SELECT COUNT(b) FROM App\Entity\Booking b ')->getSingleScalarResult();
    }

    public function getCommentsCount()
    {
        return $this->manager->createQuery(' SELECT COUNT(c) FROM App\Entity\Comment c ')->getSingleScalarResult();
    }


    public function getStats()
    {
        $users = $this->getUsersCount();
        $ads = $this->getAdsCount();
        $bookings = $this->getBookingsCount();
        $comments = $this->getCommentsCount();

        return compact('users', 'ads', 'bookings', 'comments');
    }

    public function getAdsStats($direction)
    {
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture 
            FROM App\Entity\Comment c
            JOIN c.ad a
            JOIN a.author u
            GROUP BY a
            ORDER BY note ' . $direction
        )
            ->setMaxresults(10)
            ->getResult();

    }


    public function getBestAds()
    {
        return $this->getAdsStats('DESC');
    }

    public function getWorstAds()
    {
        return $this->getAdsStats('ASC');
    }

}