<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Score;
use DateTimeImmutable;
use App\Entity\Achievement;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AchievementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: "api_")]
class APIController extends AbstractController
{

    #[Route('/achievement/save', name: 'achievement_save', methods: ['POST'])]
    public function saveAchievement(Request $request, EntityManagerInterface $em, GameRepository $gameRepository, UserRepository $userRepository): JsonResponse
    {
        // JSON-data ophalen en omzetten naar een array
        $data = json_decode($request->getContent(), true);

        // Controleer of de data geldig is
        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        // Haal specifieke velden op
        $name = $data['name'] ?? null;
        $image = $data['image'] ?? null;
        $date = $data['date'] ?? null;
        $apiKey = $data['apiKey'] ?? null;
        $userId = $data['userId'] ?? null;
        $hash = $data['hash'] ?? null;

        //vind game en user bij Id om door te geven in de achievement setters
        if (!is_string($apiKey) || !is_int($userId)) {
            return new JsonResponse(['error' => 'Invalid user or game'], 400);
        }
        $game = $gameRepository->findGameByApiKey($apiKey);
        $user = $userRepository->findUserById($userId);

        //als er geen waarde is meegegeven voor de velden worden deze op null gezet
        //we moeten dus nog een extra controle doen op de waarde null want de velden
        //mogen in de databank niet null zijn (een foutmelding terug sturen)
        if (!is_string($name) || !is_string($image) || !is_string($date) || !($game instanceof Game) || !($user instanceof User)) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        if ($game->getHash($user) !== $hash) {
            return new JsonResponse(['error' => 'vuile hacker'], 400);
        }

        $achievement = new Achievement();
        $achievement->setName($name);
        $achievement->setImage($image);
        $achievement->setDate(new DateTimeImmutable($date));
        $achievement->setGame($game);
        $achievement->setUser($user);

        $em->persist($achievement);
        $em->flush();

        return new JsonResponse([
            'message' => 'Data received successfully',
        ]);
    }


    #[Route('/achievement/get', name: 'achievement_get', methods: ['GET'])]
    public function getAchievement(AchievementRepository $achievementRepository, GameRepository $gameRepository, UserRepository $userRepository): JsonResponse
    {

        $apiKey = $_GET['apiKey'] ?? null;
        $userId = $_GET['userId'] ?? null;
        $hash = $_GET['hash'] ?? null;

        $userId = intval($userId);
        if (!is_string($apiKey) || !is_string($hash)) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        $game = $gameRepository->findGameByApiKey($apiKey);

        $user = $userRepository->findUserById($userId);

        if ($game->getHash($user) !== $hash) {
            return new JsonResponse(['error' => 'vuile hacker'], 400);
        }

        $achievements = $achievementRepository->findAchievementByGameIdAndUserId($game->getId(), $userId);
        $achievementNames = [];
        foreach ($achievements as $achievement) {
            array_push($achievementNames, $achievement->getName());
        }
        return new JsonResponse($achievementNames);
    }


    #[Route('/score/save', name: 'score_save', methods: ['POST'])]
    public function saveScore(GameRepository $gameRepository, Request $request, EntityManagerInterface $em, UserRepository $userRepository): JsonResponse
    {
        // JSON-data ophalen en omzetten naar een array
        $data = json_decode($request->getContent(), true);

        // Controleer of de data geldig is
        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        // Haal specifieke velden op
        $score = $data['score'] ?? null;
        $time = $data['time'] ?? null;
        $date = $data['date'] ?? null;
        $apiKey = $data['apiKey'] ?? null;
        $userId = $data['userId'] ?? null;
        $hash = $data['hash'] ?? null;

        //vind game en user bij Id om door te geven in de achievement setters
        if (!is_string($apiKey) || !is_int($userId)) {
            return new JsonResponse(['error' => 'Invalid userId or GameId'], 400);
        }

        $game = $gameRepository->findGameByApiKey($apiKey);
        $user = $userRepository->findUserById($userId);

        if (!is_int($score) || !is_int($time) || !is_string($date) || !is_object($game) || !is_object($user)) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        if ($game->getHash($user) !== $hash) {
            return new JsonResponse(['error' => 'vuile hacker'], 400);
        }
        $scoreObject = new Score();
        $scoreObject->setScore($score);
        $scoreObject->setTime($time);
        $scoreObject->setDate(new DateTimeImmutable($date));
        $scoreObject->setGame($game);
        $scoreObject->setUser($user);

        $em->persist($scoreObject);
        $em->flush();

        return new JsonResponse([
            'message' => 'Data received successfully',
        ]);
    }


    #[Route('/player', name: 'player_get', methods: ['GET'])]
    public function fetchPlayer(GameRepository $gameRepository, Request $request, UserRepository $userRepository): JsonResponse
    {

        $apiKey = $_GET['apiKey'] ?? null;
        $userId = $_GET['userId'] ?? null;
        $hash = $_GET['hash'] ?? null;

        $userId = intval($userId);
        if (!is_string($apiKey) || !is_string($hash)) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        $game = $gameRepository->findGameByApiKey($apiKey);

        $user = $userRepository->findUserById($userId);

        if ($game->getHash($user) !== $hash) {
            return new JsonResponse(['error' => 'vuile hacker'], 400);
        }

        //user ophalen uit databank
        $userData = [
            'username' => $user->getUsername(),
            'profilePicture' => $user->getProfilepicture()
        ];

        return new JsonResponse($userData);
    }
}
