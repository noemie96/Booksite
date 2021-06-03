<?php

namespace App\Controller;

use App\Entity\Genres;
use App\Form\GenresType;
use App\Repository\GenresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/genres")
 */
class GenresController extends AbstractController
{
    /**
     * @Route("/", name="genres_index", methods={"GET"})
     */
    public function index(GenresRepository $genresRepository): Response
    {
        return $this->render('genres/index.html.twig', [
            'genres' => $genresRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="genres_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $genre = new Genres();
        $form = $this->createForm(GenresType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($genre);
            $entityManager->flush();

            return $this->redirectToRoute('genres_index');
        }

        return $this->render('genres/new.html.twig', [
            'genre' => $genre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="genres_show", methods={"GET"})
     */
    public function show(Genres $genre): Response
    {
        return $this->render('genres/show.html.twig', [
            'genre' => $genre,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="genres_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Genres $genre): Response
    {
        $form = $this->createForm(GenresType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('genres_index');
        }

        return $this->render('genres/edit.html.twig', [
            'genre' => $genre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="genres_delete", methods={"POST"})
     */
    public function delete(Request $request, Genres $genre): Response
    {
        if ($this->isCsrfTokenValid('delete'.$genre->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($genre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('genres_index');
    }
}
