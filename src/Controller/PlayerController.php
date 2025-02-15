<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminType;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use App\Repository\FriendshipRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;


final class PlayerController extends AbstractController
{
    #[Route('/player', name: 'app_player')]
    public function index(Request $request, UserRepository $userRepository, FriendshipRepository $friendshipRepository, MessageRepository $messageRepository): Response
    {
        $search = $request->query->get('search', ''); //zoekveld
        /** @var User $user */
        $user = $this->getUser();
        $friendIds = $friendshipRepository->findFriendsIdByUserId($user->getId());


        $players = $search
            ? $userRepository->findByUsername($search) //als er iets in zit
            : $userRepository->findAll(); //als er niets in zit

        //voor een request pendinig te kunnen maken
        $sentMessages = $messageRepository->findSentMessages($user);
        $receiverIds = [];
        if (!empty($sentMessages)) {
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
        ]);
    }


    #[Route('/admin/control/{id}', name: 'app_admin_control')]
    public function regulatePlayer(int $id, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager)
    {

        if ($this->isGranted('ROLE_ADMIN')) {
            $user = $userRepository->findUserById($id);
        } else {
            throw $this->createAccessDeniedException('Page not found');
        }

        $form = $this->createForm(AdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_control', ['id' => $id]);
        }

        return $this->render('player/admin.html.twig', [
            'form' => $form,
            'user' => $user
        ]);
    }
}
