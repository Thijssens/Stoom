<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\Image;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('username')
            

            ->add('profilepicture', FileType::class, [
                'label' => 'Profile Picture',
                'constraints' => [
                    new Image([
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG or WEBP)',
                        'maxSize' => '10M', 
                        'maxSizeMessage' => 'The maximum allowed file size is 10MB.',
                    ]),
                ],
            ])

            ->add('birthday', BirthdayType::class, [
                'widget' => 'choice', // Dropdowns for day, month, year
                'format' => 'yyyy-MM-dd', // Adjust to match your expected date format
                'placeholder' => [
                    'year' => 'Year', 
                    'month' => 'Month', 
                    'day' => 'Day'
                ],
                'input' => 'datetime_immutable', // Ensure data is converted to DateTimeImmutable
                'required' => false, // Makes the field optional
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Male' => 'male',
                    'Female' => 'female',
                    'Other' => 'other',
                ],
                'placeholder' => 'Select your gender', // Placeholder for dropdown
                'required' => false, // Makes the field optional
                'expanded' => false, // Use a dropdown (default)
                'multiple' => false, // Single choice only
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])

            // van chatgpt begrijp nog niet
            ->add('confirmPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please confirm your password',
                    ]),
                    new Callback([
                        'callback' => function ($confirmPassword, ExecutionContextInterface $context) {
                            $form = $context->getRoot();
                            $plainPassword = $form->get('plainPassword')->getData();

                            if ($plainPassword !== $confirmPassword) {
                                $context->buildViolation('Passwords do not match.')
                                    ->addViolation();
                            }
                        },
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
