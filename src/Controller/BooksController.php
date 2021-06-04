<?php

namespace App\Controller;

use App\Entity\Books;
use App\Entity\Genres;
use App\Entity\Comment;
use App\Form\AnnonceType;
use App\Form\CommentType;
use App\Form\AnnonceEditType;
use App\Repository\BooksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function create(EntityManagerInterface $manager, Request $request)
    {
        $books = new Books();
        
        
        $form = $this->createForm(AnnonceType::class,$books);

        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){

            $books->setUtilisateur($this->getUser());
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

    /**
     * Permet de modifier une fiche de livre
     * @Route("books/{slug}/edit", name="books_edit")
     * @Security("(is_granted('ROLE_USER') and user === books.getUtilisateur()) or is_granted('ROLE_ADMIN')", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Books $books
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, Books $books)
    {
        $form = $this->createForm(AnnonceEditType::class, $books);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $books->setSlug('');

            foreach($books->getCoverImage() as $coverImage){
                $coverImage->setBooks($books);
                $manager->persist($coverImage);
            }

            

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
    public function show(Books $books, Request $request, EntityManagerInterface $manager)
    {
        
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $comment->SetBook($books)
                    ->setUtilisateur($this->getUser());
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre commentaire a bien été pris en compte! '
            );
        }

        

        return $this->render('books/book.html.twig',[
            'books' => $books,
            'myForm' => $form->createView()
        ]);

    }
    
    /**
     * Permet de supprimer une fiche livre
     * @Route("/books/{slug}/delete", name="books_delete")
     * @Security("(is_granted('ROLE_USER') and user === books.getUtilisateur()) or is_granted('ROLE_ADMIN')", message="Vous n'avez pas le droit d'accèder à cette ressource")
     * @param Books $books
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete (Books $books, EntityManagerInterface $manager)
    {
        $this->addFlash(
            'success',
            "La fiche de <strong>{$books->getTitle()}</strong>à bien été supprimée"
        );
        $manager->remove($books);
        $manager->flush();
        return $this->redirectToRoute("books_index");
    }



}
