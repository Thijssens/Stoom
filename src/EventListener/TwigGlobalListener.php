<?php

namespace App\EventListener;

use App\Repository\FriendshipRepository;
use Twig\Environment;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\MessageRepository;

class TwigGlobalListener implements EventSubscriberInterface
{
    private Environment $twig;
    private MessageRepository $messageRepository;
    private Security $security;
    //bijgedaan om message pas te tonen wanneer je een vriend hebt
    private FriendshipRepository $friendshipRepository;

    public function __construct(Environment $twig, MessageRepository $messageRepository, Security $security, FriendshipRepository $friendshipRepository)
    {
        $this->twig = $twig;
        $this->messageRepository = $messageRepository;
        $this->security = $security;
        $this->friendshipRepository = $friendshipRepository;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $user = $this->security->getUser();

        if ($user) {
            $unreadMessages = count($this->messageRepository->findUnreadMessages($user));
            $this->twig->addGlobal('unreadMessagesCount', $unreadMessages);

            $this->twig->addGlobal('userEmail', $user->getUserIdentifier());
            //om de knop message te tonen of niet
            $numberOfFriends = count($this->friendshipRepository->findFriendsIdByUserId($user));
            $this->twig->addGlobal('numberOfFriends', $numberOfFriends);
        } else {
            $this->twig->addGlobal('unreadMessagesCount', 0);
            $this->twig->addGlobal('userEmail', 'My Profile');
            $this->twig->addGlobal('numberOfFriends', 0);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }
}

//cache nog leegmaken voor eerste gebruik
//php bin/console cache:clear