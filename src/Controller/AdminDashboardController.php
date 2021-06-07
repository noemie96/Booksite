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
        $bookss = $statsService->getAdsCount();
        $comments = $statsService->getCommentsCount();

        $bestBookss = $statsService->getBooksStats('DESC');
        $worstBookss = $statsService->getBooksStats('ASC');

        // 'stats' => compact('users','books','comments')

        return $this->render('admin/dashboard/index.html.twig', [
          'stats' => [
              'users' => $users,
              'bookss' => $bookss,
              'comments' => $comments
          ],
          'bestBookss' => $bestbookss,
          'worstBookss' => $worstBookss
        ]);
    }
}