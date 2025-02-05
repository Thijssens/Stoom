<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\FriendshipRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// use function Symfony\Component\Clock\now;

#[Route('/message')]
final class MessageController extends AbstractController
{
    #[Route('/{id}', name: 'message_index')]

    public function index(int $id, Request $request, ?User $receiver, EntityManagerInterface $em, UserRepository $userRepository, MessageRepository $messageRepository, FriendshipRepository $friendshipRepository): Response
    {
        $sender = $this->getUser();
        $receiver = $userRepository->findUserById($id);

        //OPHALEN VRIENDEN VOOR DROPDOWN
        $friendIds = $friendshipRepository->findFriendsIdByUserId($sender);
        $friends = $userRepository->findFriendsByIds($friendIds);


        //OPHALEN CONVERSATIE
        if (!isset($receiver)) {
            $conversation = [];
        }

        if (isset($receiver)) {
            $conversation = $messageRepository->findMessagesBetweenUsers($sender, $receiver);
        }



        // NIEUW BERICHT VERSTUREN
        $message = new Message();
        $message->setSender($sender);
        $message->setReceiver($receiver);

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $message->setCreatedAt(new DateTimeImmutable('now'));
            $message->setIsRead(false);
            $em->persist($message);
            $em->flush();
            //refresh van de pagina
            return new RedirectResponse($request->getUri());
        }

        return $this->render('message/index.html.twig', [
            'form' => $form->createView(),
            'conversation' => $conversation,
            'receiverId' => $id,
            'friends' => $friends,
        ]);
    }
}
