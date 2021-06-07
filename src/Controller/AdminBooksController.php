<?php

namespace App\Controller;

use App\Entity\Books;
use App\Form\AnnonceType;
use App\Form\AnnonceEditType;
use App\Entity\CoverImageModify;
use App\Service\PaginationService;
use App\Repository\BooksRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AnnonceCoverImageModifyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminBooksController extends AbstractController
{
    /**
     * Permet d'afficher l'ensemble des annonces
     * @Route("/admin/books/{page<\d+>?1}", name="admin_books_index")
     */
    public function index($page, PaginationService $pagination): Response
    {
        $pagination->setEntityClass(Books::class)
                    ->setPage($page)
                    ->setLimit(10)
                    ->setRoute('admin_books_index');
                        /*setRoute est optionnel */

        /*
        return $this->render('admin/books/index.html.twig', [
            'bookss' => $pagination->getData(),
            'pages' => $pagination->getPages(),
            'page' => $page
        ]);
        */
        return $this->render('admin/books/index.html.twig',[
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     * @Route("/admin/books/{id}/edit", name="admin_books_edit")
     * 
     * @param Books $books
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Books $books, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AnnonceEditType::class, $books);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($books);
            $manager->flush();

            $this->addFlash(
                "success",
                "L'annonce <strong>{$books->getTitle()}</strong> a bien été modifiée"
            );
        }

        return $this->render("admin/books/edit.html.twig",[
            'books' => $books,
            'myForm' => $form->createView()
        ]);
    }


    /**
     * Permet de modifier l'image de couverture
     * @Route("admin/books/{slug}/CoverImageModify", name="admin_books_coverimagemodify")
     * @param Books $books
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function coverImageModify(Books $books, Request $request, EntityManagerInterface $manager){

        $coverImageModify = new CoverImageModify();
        $form = $this->createForm(AnnonceCoverImageModifyType::class, $coverImageModify);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if(!empty($books->getCoverImage())){
                unlink($this->getParameter('uploads_directory').'/'.$books->getCoverImage());
            }
        $file = $form['CoverImageModify']->getData();
        if(!empty($file)){
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
            try{
                $file->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
            }
            catch(FileException $e)
            {
                return $e->getMessage();
            }

            $books->setCoverImage($newFilename);
        }
        $manager->persist($books);
            $manager->flush();

            $this->addFlash(
                'success',
                "la couverture du livre <strong>{$books->getTitle()}</strong> a bien été modifiée"
            );

            return $this->redirectToRoute('books_detail',[
                'slug' =>$books->getSlug('')
            ]);
        }

        return $this->render("books/edit.html.twig",[
            "books" =>$books,
            "myForm" =>$form->createView()
        ]);
    
    }


    /**
     * Permet de supprimer une annonce
     * @Route("/admin/books/{id}/delete", name="admin_books_delete")
     *
     * @param Books $books
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Books $books, EntityManagerInterface $manager){
        // one ne peut pas supprimer une annonce qui possède des avis
        
            $this->addFlash(
                'success',
                "La fiche de <strong>{$books->getTitle()}</strong>à bien été supprimée"
            );
            $manager->remove($books);
            $manager->flush();
            return $this->redirectToRoute("admin_books_index");
        }
}
