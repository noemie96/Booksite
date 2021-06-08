<?php

namespace App\Controller;

use App\Service\StatsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard_index")
     */
    public function index(StatsService $statsService): Response
    {
      
        $users = $statsService->getUsersCount();
        $books = $statsService->getBooksCount();
        $comments = $statsService->getCommentsCount();

        $bestBooks = $statsService->getBooksStats('DESC');
        $worstBooks = $statsService->getBooksStats('ASC');

        // 'stats' => compact('users','books','comments')

        return $this->render('admin/dashboard/index.html.twig', [
          'stats' => [
              'users' => $users,
              'books' => $books,
              'comments' => $comments
          ],
          'bestBooks' => $bestbooks,
          'worstBookss' => $worstBookss
        ]);
    }
}