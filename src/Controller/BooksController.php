<?php

namespace App\Controller;

use App\Entity\Books;
use App\Repository\BooksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BooksController extends AbstractController
{
    /**
     * @Route("/books", name="books_index")
     */
    public function index(BooksRepository $repo): Response
    {
        $bookss = $repo->findAll();

        return $this->render('books/index.html.twig', [
            'bookss' => $bookss,
        ]);
    }

    /**
     * Permet d'afficher un seul livre
     * @Route("/books/{slug}", name="books_detail")
     *
     * @param [string] $slug
     * @return Response
     */
    public function show(Books $books)
    {
        //$repo = $this->getDoctrine()->getRepository(Books::class);
        //$ad = $repo->findOneBySlug($slug);


        //dump($ad);

        return $this->render('books/book.html.twig',[
            'books' => $books
        ]);

    }
  



}
