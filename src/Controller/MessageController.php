<?php
// src/Controller/MessageController.php
namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/message')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'messages_list')]
    public function index(MessageRepository $messageRepository): Response
    {
        $user = $this->getUser();
        $messages = $messageRepository->findBy(['receiver' => $user], ['createdAt' => 'DESC']);

        return $this->render('message/index.html.twig', [
            'messages' => $messages,
        ]);
    }

    #[Route('/new/{id}', name: 'message_new')]
    public function new(int $id, Request $request, User $receiver, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        $message = new Message();
        $message->setSender($this->getUser());
        $receiver = $userRepository->findUserById($id); //MOET NOG AANGEPAST WORDEN
        $message->setReceiver($receiver);

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setCreatedAt(new DateTimeImmutable("now"));
            $message->setIsRead(false);
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('messages_list');
        }

        return $this->render('message/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

