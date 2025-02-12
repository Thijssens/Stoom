<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\AchievementRepository;
use App\Repository\GameRepository;
use App\Repository\ScoreRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/profile')]
final class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile_show')]
    public function show(GameRepository $gameRepository, AchievementRepository $achievementRepository, ScoreRepository $scoreRepository): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            /** @var User $user */
            $user = $this->getUser();
            $games = $gameRepository->findGamesByUserId($user->getId());
            $achievements = $achievementRepository->findAchievementByUserId($user->getId());
            $lowestTimes = $scoreRepository->findLowestTimeByUserId($user->getId()); // geeft een array terug om de 1 of andere reden
            $lowestTime = $lowestTimes[0][1];
            $highestScores = $scoreRepository->findHighestScoreByUserId($user->getId());
            $highestScore = $highestScores[0][1];

            $playedGames = $scoreRepository->countPlayedGamesByUserId($user->getId());
            $numberOfPlayedGames = $playedGames[0][1];
        }



        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to access this page.');
        }

        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'games' => $games,
            'achievements' => $achievements,
            'lowestTime' => $lowestTime,
            'highestScore' => $highestScore,
            'numberOfPlayedGames' => $numberOfPlayedGames
        ]);
    }

    #[Route('/edit', name: 'app_profile_edit')]
    public function edit(UserPasswordHasherInterface $userPasswordHasher, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            /** @var User $user */
            $user = $this->getUser();
        }

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to edit your profile.');
        }

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('profilepicture')->getData();

            if (isset($file)) {

                $file->getPathname();
                $to = 'uploads/' .  $file->getClientOriginalName();
                move_uploaded_file($file->getPathname(), $to);
                $user->setProfilepicture($to);
            }

            $plainPassword = $form->get('plainPassword')->getData();

            if (isset($plainPassword)) {

                // encode the plain password
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }

            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_profile_show');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form,
            'user' => $user
        ]);
    }
}
