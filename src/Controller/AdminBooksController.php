<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBooksController extends AbstractController
{
    /**
     * @Route("/admin/books", name="admin_books_index")
     */
    public function index(): Response
    {
        return $this->render('admin/books/index.html.twig', [

        ]);
    }
}
