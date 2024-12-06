<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Repository\UserRepository;

#[Route(path: '/leaderboard')]
class LeaderboardController extends AbstractController
{
    #[Route(path: '/view', name: 'app_leaderboard_view')]
    public function view(UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to access the leaderboard.');
        }

        return $this->render('leaderboard/view.html.twig', [
            'actualUser' => $user,
            'actualUserPostion' => $userRepository->getUserRank($user->getId()),
            'users' => $userRepository->getUserLeaderboard(),
        ]);
    }
}