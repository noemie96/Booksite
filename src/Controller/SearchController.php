<?php

namespace App\Controller;

use App\Entity\Books;
use App\Repository\BooksRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index(BooksRepository $repo, Request $request): Response
    {
        
        $bookss = $repo->mySearch($request->query->get('search'));
        
        
        return $this->render('books/search.html.twig', [
            'bookss' => $bookss,
            'recherche' => $request->query->get('search')
            
            ]);
    }

}
