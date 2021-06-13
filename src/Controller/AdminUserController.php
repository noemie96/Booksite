<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/user/{page<\d+>?1}", name="admin_user")
     */
    public function index($page, PaginationService $pagination): Response
    {
        $pagination->setEntityClass(User::class)
                    ->setPage($page)
                    ->setLimit(10);

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination        
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition d'un user et modifier ses informations
     * @Route("admin/user/index", name="admin_user_profile")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $manager)
    {

        $user = $this->getUser(); // récupérer l'utilisateur connecté 
        $fileName = $user->getPicture();
        if(!empty($fileName))
        {
            $user->setPicture(
                new File($this->getParameter('uploads_directory').'/'.$user->getPicture())
            );
        }
        
        $form = $this->createForm(AccountType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user->setSlug('')
                ->setPicture($fileName);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                'Les données ont été modifiée avec succés'
            );
            return $this->redirectToRoute('admin/user/index.html.twig');
        }
        return $this->render("admin/user/index.html.twig",[
            'myForm' => $form->createView()
        ]);
    }



    /** 
    * Permet de supprimer une annonce
    * @Route("/admin/user/{id}/delete", name="admin_user_delete")
    *
    * @param Books $books
    * @param EntityManagerInterface $manager
    * @return Response
    */
   public function delete(User $user, EntityManagerInterface $manager){
       // one ne peut pas supprimer une annonce qui possède des avis
       
           $this->addFlash(
               'success',
               "La fiche de <strong>{$user->getFullName()}</strong>à bien été supprimée"
           );
           $manager->remove($user);
           $manager->flush();
           return $this->redirectToRoute("admin_user");
       }


    


}
