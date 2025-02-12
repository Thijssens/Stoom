<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Form\GameType;
use App\Repository\GameRepository;
use App\Repository\FriendshipRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/')]
final class GameController extends AbstractController
{
    #[Route(name: 'app_game_index', methods: ['GET'])]
    public function index(GameRepository $gameRepository, Security $security, FriendshipRepository $friendshipRepository, MessageRepository $messageRepository): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            /** @var User $user */      //zo maken we gebruik van het echte user object
            $user = $this->getUser();
            // $unreadMessages = $messageRepository->findUnreadMessages($user);
        }

        //dd($this->getUser());
        if ($security->isGranted('ROLE_ADMIN')) {
            $retrievedGames = $gameRepository->findAll();
            $games = $this->setFullLink($retrievedGames, $user);


            return $this->render('game/index.html.twig', [
                'games' => $games,
                'user' => $user,
                // 'unreadMessages' => count($unreadMessages),
            ]);
        } elseif ($security->isGranted('ROLE_USER')) {
            $friendIds = $friendshipRepository->findFriendsIdByUserId($user->getId());
            $retrievedGames = $gameRepository->findViewableGames($user->getId(), $friendIds);
            $games = $this->setFullLink($retrievedGames, $user);

            return $this->render('game/index.html.twig', [
                'games' => $games,
                'user' => $user
                // 'unreadMessages' => count($unreadMessages),
            ]);
        } else {
            return $this->render('game/index.html.twig', [
                'games' => $gameRepository->getPublicGames(),
            ]);
        }
    }


    #[Route('/game/friends', name: 'app_game_friends', methods: ['GET'])]
    public function showFriendGames(GameRepository $gameRepository, FriendshipRepository $friendshipRepository)
    {

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            /** @var User $user */
            $user = $this->getUser();
        }

        $friends = $friendshipRepository->findFriendsByUserId($user->getId());
        $games = [];
        foreach ($friends as $friend) {
            $games += $gameRepository->findGamesByUserId($friend->getFriendId());
        }


        return $this->render('game/friends.html.twig', ['games' => $games]);
    }

    #[Route('/game/new', name: 'app_game_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            /** @var User $user */
            $user = $this->getUser();
            $userID = $user->getId();
            $game->setOwner($userID);
            $game->setApiKey($game->generateApiKey());

            $file = $form->get('thumbnail')->getData();

            $file->getPathname();
            $to = 'uploads/' .  $file->getClientOriginalName();
            move_uploaded_file($file->getPathname(), $to);
            $game->setThumbnail($to);


            // $user = $this->getUser();
            // $id = $user->getId();


            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game/new.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/game/{id}', name: 'app_game_show', methods: ['GET'])]
    public function show(Game $game, Security $security): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$security->isGranted('ROLE_ADMIN') && $game->getOwner() !== $user->getId()) {
            die('Game not found');
        }
        return $this->render('game/show.html.twig', [
            'game' => $game,
        ]);
    }

    #[Route('/game/{id}/edit', name: 'app_game_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Game $game, EntityManagerInterface $entityManager, Security $security): Response
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);
        /** @var User $user */
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('thumbnail')->getData();

            $file->getPathname();
            $to = 'uploads/' .  $file->getClientOriginalName();
            move_uploaded_file($file->getPathname(), $to);
            $game->setThumbnail($to);

            $entityManager->flush();

            return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
        }

        // Check if the game belongs to the authenticated user
        if (!$security->isGranted('ROLE_ADMIN') && $game->getOwner() !== $user->getId()) {
            die('Game not found');
        }

        return $this->render('game/edit.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/game/{id}', name: 'app_game_delete', methods: ['POST'])]
    public function delete(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $game->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($game);
            $entityManager->flush();
        }


        return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
    }

    public function setFullLink(array $games, User $user): array
    {

        foreach ($games as $game) {
            $gameLink = $game->getFullLink($user);
            $game->setLink($gameLink);
        }
        return $games;
    }
}
