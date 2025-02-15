<?php

namespace App\Controller;

use App\Entity\User;
use Twig\Environment;
use DateTimeImmutable;
use App\Entity\Message;
use App\Form\MessageType;
use App\Entity\Friendship;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use App\Repository\FriendshipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// use function Symfony\Component\Clock\now;

#[Route('/message')]
final class MessageController extends AbstractController
{
    #[Route('/{id}', name: 'message_index')]

    public function index(int $id, Request $request, ?User $receiver, EntityManagerInterface $em, UserRepository $userRepository, MessageRepository $messageRepository, FriendshipRepository $friendshipRepository, Environment $twig): Response
    {
        $sender = $this->getUser();
        $receiver = $userRepository->findUserById($id);
        $friendRequests = [];

        //OPHALEN VRIENDEN VOOR DROPDOWN
        $friendIds = $friendshipRepository->findFriendsIdByUserId($sender);
        $friends = $userRepository->findFriendsByIds($friendIds);

        //checken of we vrienden zijn met de receiver(friend request)

        //OPHALEN CONVERSATIE
        if (!isset($receiver)) {
            $conversation = [];
            //kijken of we ongelezen berichten hebben van NIET vrienden -> friend requests
            $unreadMessages = $messageRepository->findUnreadMessages($sender);
            $unreadMessagesCount = count($unreadMessages);
            if ($unreadMessagesCount > 0) {
                //nagaan of de message van een NIET vriend is (moeten we opvangen omdat als we via de knop message
                //naar de messageController gaan we de id 0 meegeven en de conversatie aldus ook leeg is omdat er 
                //nog geen vriend is gelesecteerd)
                foreach ($unreadMessages as $message) {
                    if (in_array($message->getSender(), $friends) == false) {
                        array_push($friendRequests, $message);
                    }
                }
            }
        }

        if (isset($receiver)) {
            $conversation = $messageRepository->findMessagesBetweenUsers($sender, $receiver);
            foreach ($conversation as $message) {
                if ($message->getReceiver() == $sender) {
                    $this->updateMessage($message, $em);
                }
            }
            //nagaan of de ongelezen berichten van deze conversatie zijn en melding ongelezen berichten verwijderen 
            $unreadMessagesCount = count($messageRepository->findUnreadMessages($sender));
            $twig->addGlobal('unreadMessagesCount', $unreadMessagesCount);
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
            'receiver' => $receiver,
            'friendRequests' => $friendRequests,
            'sender' => $sender
        ]);
    }

    #[Route('/friendRequest/{id}', name: 'message_friendRequest')]
    public function sentRequest(int $id, UserRepository $userRepository, EntityManagerInterface $em, Request $request)
    {
        $sender = $this->getUser();
        $receiver = $userRepository->findUserById($id);
        $message = new Message();
        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setContent('Friend request sent');
        $message->setCreatedAt(new DateTimeImmutable('now'));
        $message->setIsRead(false);

        $em->persist($message);
        $em->flush();
        return $this->redirectToRoute('app_player');
    }

    #[Route('/friendRequest/accept/{id}', name: 'message_request_accept')]
    public function acceptRequest(int $id, UserRepository $userRepository, EntityManagerInterface $em)
    {
        /** @var User $user */
        $user = $this->getUser();
        $friend = $userRepository->findUserById($id);

        //vriendschap aanmaken in friendship tabel (2x)
        $friendship = new Friendship();
        $friendship->setUserId($user->getId());
        $friendship->setFriendId($id);
        $em->persist($friendship);
        $em->flush();

        $friendship = new Friendship();
        $friendship->setUserId($id);
        $friendship->setFriendId($user->getId());
        $em->persist($friendship);
        $em->flush();

        //bericht sturen met verzoek aanvaard
        $message = new Message();
        $message->setSender($user);
        $message->setReceiver($friend);
        $message->setContent('Friend request accepted');
        $message->setCreatedAt(new DateTimeImmutable('now'));
        $message->setIsRead(false);
        $em->persist($message);
        $em->flush();

        return $this->redirectToRoute('message_index', ['id' => 0]);
    }

    #[Route('/friendRequest/decline/{id}', name: 'message_request_decline')]
    public function declineRequest(int $id, EntityManagerInterface $entityManager, MessageRepository $messageRepository)
    {
        //bericht uit db halen
        $message = $messageRepository->findMessageById($id);
        $entityManager->remove($message);
        $entityManager->flush();
        return $this->redirectToRoute('message_index', ['id' => 0]);
    }


    public function updateMessage($message, EntityManagerInterface $entityManagerInterface)
    {
        $message->setIsRead(true);
        $entityManagerInterface->flush();
    }
}
