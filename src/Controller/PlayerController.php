<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PlayerController extends AbstractController
{
    #[Route('/player', name: 'app_player')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $search = $request->query->get('search', '');
        $user = $this->getUser();

        $players = $search
            ? $userRepository->findByUsername($search)
            : $userRepository->findAll();

        return $this->render('player/index.html.twig', [
            'players' => $players,
            'user' => $user,
            'search' => $search,
        ]);
    }
}
