<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\BooksRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(BooksRepository $booksRepo, UserRepository $userRepo): Response
    {
        return $this->render('home/index.html.twig',[
            'books' => $booksRepo->findBestBooks(3),
            'users' => $userRepo->findBestUsers(2)
        ]);
    }
}