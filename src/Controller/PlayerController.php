<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use App\Repository\FriendshipRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PlayerController extends AbstractController
{
    #[Route('/player', name: 'app_player')]
    public function index(Request $request, UserRepository $userRepository, FriendshipRepository $friendshipRepository, MessageRepository $messageRepository): Response
    {
        $search = $request->query->get('search', '');
        /** @var User $user */
        $user = $this->getUser();
        $friendIds = $friendshipRepository->findFriendsIdByUserId($user->getId());


        $players = $search
            ? $userRepository->findByUsername($search)
            : $userRepository->findAll();


        $sentMessages = $messageRepository->findSentMessages($user);
        $receiverIds = [];
        if (isset($sentMessages)) {
            foreach ($sentMessages as $message) {
                array_push($receiverIds, $message->getReceiver()->getId());
            }
        }

        return $this->render('player/index.html.twig', [
            'players' => $players,
            'user' => $user,
            'search' => $search,
            'friendsIds' => $friendIds,
            'receiverIds' => $receiverIds,
            // 'sentMessages' => $sentMessages,
        ]);
    }
}
