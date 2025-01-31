<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PlayerController extends AbstractController
{
    #[Route('/player', name: 'app_player')]
    public function index(UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        return $this->render('player/index.html.twig', [
            'players'=> $userRepository->findAll(),
            'user'=>$user,
        ]);
    }
}
