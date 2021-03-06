<?php

namespace App\Controller;

use App\Entity\Books;
use App\Entity\Genres;
use App\Entity\Comment;
use App\Form\AnnonceType;
use App\Form\CommentType;
use App\Entity\UserImgModify;
use App\Form\AnnonceEditType;
use App\Entity\CoverImageModify;
use App\Form\AnnonceCoverImageModifyType;
use App\Repository\BooksRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Validator\Constraints\All;

class BooksController extends AbstractController
{
    /**
     * @Route("/books/{filtre}/{page<\d+>?1}", name="books_index")
     */
    public function index($page, PaginationService $pagination, $filtre="All"): Response
    {
        $filtres=["All", "Thriller","Fantasy","Fantastique","Policier","Romance","Horreur", "Classique", "Contemporain", "Sciences-fiction"];
        if(in_array($filtre, $filtres)){
            if($filtre == "All"){
                $pagination->setEntityClass(Books::class)
                            ->setPage($page)
                            ->setLimit(10);
            }else{
                $pagination->setEntityClass(Books::class)
                ->setPage($page)
                ->setCategory($filtre)
                ->setLimit(10);

            }
        }else{
            throw $this->createNotFoundException("catégorie inconnue");
        }

        

        return $this->render('books/index.html.twig', [
            'pagination' => $pagination,
            'filtre' => $filtre
        ]);
    }

    /**
     * Permet de créer une fiche de nouveau livre
     * @Route("/books-new", name="books_create")
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

            $file = $form['coverImage']->getData();
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
     * @Route("books-edit/{slug}", name="books_edit")
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
     * Permet de modifier l'image de couverture
     * @Route("/books-edit/{slug}/CoverImageModify", name="books_coverimagemodify")
     * @IsGranted("ROLE_USER")
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
     * Permet d'afficher un seul livre
     * @Route("/books-view/{slug}", name="books_detail")
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
     * @Route("/books-delete/{slug}", name="books_delete")
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
