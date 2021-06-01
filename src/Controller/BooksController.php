<?php

namespace App\Controller;

use App\Entity\Books;
use App\Form\AnnonceType;
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
     * Permet de créer une fiche de nouveau livre
     * @Route("/books/new", name="books_create")
     *
     * @return Response
     */
    public function create(EntityManagerInterface $manager, Request $request)
    {
        $books = new Books();
       
        $form = $this->createForm(AnnonceType::class,$books);

        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $manager->persist($books);
            $manager->flush();

            $this->addFlash(
                'success',
                "la fiche pour <strong>{$books->getTitle()}</strong> a bien été enregistrée"
            );

            return $this->redirectToRoute('books_detail',[
                'slug' =>$books->getSlug()
            ]);
        }
            return $this->render('books/new.html.twig',[
                'myForm' =>$form->createView()
            ]);
            }


    public function edit(Request $request, EntityManagerInterface $manager, Books $books)
    {
        $form = $this->createForm(AnnonceEditType::class, $books);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $books->setSlug('');

            foreach($books->getImages() as $image){
                $image->setBooks($books);
                $manager->persist($image);
            }

            //on ajoute l'auteur mais attention maintenant il y a un risque de bug si on n'est pas connecté (à corriger)
            $books->setUtilisateur($this->getUser());

            $manager->persist($books);
            $manager->flush();

            $this->addFlash(
                'success',
                "la fiche du livre <strong>{$books->getTitle()}</strong> a bien été modifiée"
            );

            return $this->redirectToRoute('books_detail',[
                'slug' =>$books->getSlug()
            ]);
        }

        return $this->render("books/edit.html.twig",[
            "books" =>$books,
            "myForm" =>$form->createView()
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
