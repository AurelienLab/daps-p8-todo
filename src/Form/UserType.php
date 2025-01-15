<?php

namespace App\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class UserType extends AbstractType
{


    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['data'];
        $isCreation = !$this->entityManager->contains($user);
        $builder
            ->add(
                'username', TextType::class, [
                              'label' => "Nom d'utilisateur",
                          ]
            )
            ->add(
                'password', RepeatedType::class, [
                              'type' => PasswordType::class,
                              'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                              'required' => $isCreation,
                              'first_options' => [
                                  'label' => 'Mot de passe',
                                  'help' => !$isCreation ? 'Laisser les champs vides pour ne pas modifier le mot de passe' : null
                              ],
                              'second_options' => ['label' => 'Tapez le mot de passe à nouveau'],
                          ]
            )
            ->add(
                'roles', ChoiceType::class, [
                           'label' => 'Rôle',
                           'choices' => [
                               'Utilisateur' => 'ROLE_USER',
                               'Administrateur' => 'ROLE_ADMIN'
                           ],
                           'mapped' => false,
                           'required' => true,
                           'data' => $builder->getData()->getRoles()[0],
                           'constraints' => [
                               new NotBlank(),
                               new NotNull()
                           ]
                       ]
            )
            ->add('email', EmailType::class, ['label' => 'Adresse email'])
        ;
    }


}
