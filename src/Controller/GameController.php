<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Form\GameType;
use App\Repository\GameRepository;
use App\Repository\FriendshipRepository;
use App\Repository\MessageRepository;
use App\Repository\ScoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/')]
final class GameController extends AbstractController
{
    #[Route(name: 'app_game_index', methods: ['GET'])]
    public function index(GameRepository $gameRepository, Security $security, FriendshipRepository $friendshipRepository): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            /** @var User $user */
            $user = $this->getUser();

            if ($user->isBlocked() === true) {
                return $this->redirectToRoute('app_logout');
            }

            if ($security->isGranted('ROLE_ADMIN')) {
                $retrievedGames = $gameRepository->findAll();
                $games = $this->setFullLink($retrievedGames, $user);

                return $this->render('game/index.html.twig', [
                    'games' => $games,
                    'user' => $user,
                ]);
            }

            if ($security->isGranted('ROLE_USER')) {
                $friendIds = $friendshipRepository->findFriendsIdByUserId($user->getId());
                $retrievedGames = $gameRepository->findViewableGames($user->getId(), $friendIds);
                $games = $this->setFullLink($retrievedGames, $user);

                return $this->render('game/index.html.twig', [
                    'games' => $games,
                    'user' => $user
                ]);
            }

            return $this->render('game/index.html.twig', [
                'games' => $gameRepository->getPublicGames(),
            ]);
        }

        return $this->render('game/index.html.twig', [
            'games' => $gameRepository->getPublicGames(),
        ]);
    }


    #[Route('/game/friends', name: 'app_game_friends', methods: ['GET'])]
    public function showFriendGames(GameRepository $gameRepository, FriendshipRepository $friendshipRepository)
    {

        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('You must be logged in.');
        }
        /** @var User $user */
        $user = $this->getUser();
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
        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('You must be logged in.');
        }
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

            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('app_game_index');
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

        //enkel owners of admins kunnen show game zien
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
            $path = $game->getThumbnail();
            if (file_exists($path)) {
                unlink($path);
            }
            $entityManager->remove($game);
            $entityManager->flush();
        }


        return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/data/json', name: 'json_data')]
    public function getJsonData(): JsonResponse
    {
        $data = [
            "info" => [
                "_postman_id" => "b054f309-c050-49b1-b083-2bb3cc4e6d30",
                "name" => "Stoom",
                "schema" => "https://schema.getpostman.com/json/collection/v2.1.0/collection.json",
                "_exporter_id" => "38227772"
            ],
            "item" => [
                [
                    "name" => "Save achievements",
                    "request" => [
                        "method" => "POST",
                        "header" => [],
                        "body" => [
                            "mode" => "raw",
                            "raw" => "{\r\n    \"name\": \"string\",\r\n    \"image\": \"string\",\r\n    \"date\": \"string\",\r\n    \"apiKey\": \"string\",\r\n    \"userId\": \"int\",\r\n    \"hash\": \"string\"\r\n}",
                            "options" => ["raw" => ["language" => "json"]]
                        ],
                        "url" => [
                            "raw" => "{{url}}/achievement/save",
                            "host" => ["{{url}}"],
                            "path" => ["achievement", "save"]
                        ]
                    ],
                    "response" => []
                ],
                [
                    "name" => "Save score",
                    "request" => [
                        "method" => "POST",
                        "header" => [],
                        "body" => [
                            "mode" => "raw",
                            "raw" => "{\r\n    \"score\": \"int\",\r\n    \"time\": \"int\",\r\n    \"date\": \"string\",\r\n    \"apiKey\": \"string\",\r\n    \"userId\": \"int\",\r\n    \"hash\": \"string\"\r\n}",
                            "options" => ["raw" => ["language" => "json"]]
                        ],
                        "url" => [
                            "raw" => "{{url}}/score/save",
                            "host" => ["{{url}}"],
                            "path" => ["score", "save"]
                        ]
                    ],
                    "response" => []
                ],
                [
                    "name" => "Achievements",
                    "request" => [
                        "method" => "GET",
                        "header" => [],
                        "url" => [
                            "raw" => "{{url}}/achievement/get?apiKey=&userId=&hash=",
                            "host" => ["{{url}}"],
                            "path" => ["achievement", "get"],
                            "query" => [
                                ["key" => "apiKey", "value" => ""],
                                ["key" => "userId", "value" => ""],
                                ["key" => "hash", "value" => ""]
                            ]
                        ]
                    ],
                    "response" => []
                ],
                [
                    "name" => "Player info",
                    "request" => [
                        "method" => "GET",
                        "header" => [],
                        "url" => [
                            "raw" => "{{url}}/player?apiKey=&userId=&hash=",
                            "host" => ["{{url}}"],
                            "path" => ["player"],
                            "query" => [
                                ["key" => "apiKey", "value" => ""],
                                ["key" => "userId", "value" => ""],
                                ["key" => "hash", "value" => ""]
                            ]
                        ]
                    ],
                    "response" => []
                ]
            ]
        ];

        return new JsonResponse($data);
    }


    #[Route('/game/{id}/leaderboard/{orderBy}/{direction}', name: 'app_game_leaderboard')]
    public function showLeaderboard(int $id, string $orderBy, string $direction, ScoreRepository $scoreRepository): Response
    {


        $gameScores = $scoreRepository->findPlayedGamesByGameIdOrderBy($id, $orderBy, $direction);

        return $this->render('game/leaderboard.html.twig', [
            "gameId" => $id,
            "gameScores" => $gameScores,
        ]);
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
