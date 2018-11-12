<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(AdRepository $adRepo, UserRepository $userRepo)
    {
        $ads = $adRepo->findBestAds(9);
        $users = $userRepo->findBestUsers(3);
        return $this->render('home/index.html.twig', [
            'ads' => $ads,
            'users' => $users,
        ]);
    }

}
