<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $file = $form->get('profilepicture')->getData();

            if (isset($file)) {

                $file->getPathname();
                $to = 'uploads/' .  $file->getClientOriginalName();
                move_uploaded_file($file->getPathname(), $to);
                $user->setProfilepicture($to);
            }

            if (!isset($file)) {
                $user->setProfilepicture('uploads/Dummy_User.jpg');
            }


            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();



            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_USER']);
            $user->setIsadmin(false);
            $user->setIsBlocked(false);
            $user->setIsMuted(false);
            $user->setIsRestrictedFromFriendRequests(false);
            $entityManager->persist($user);
            $entityManager->flush();

            return $security->login($user);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
