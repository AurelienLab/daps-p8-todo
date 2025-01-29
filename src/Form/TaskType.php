<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TaskType extends AbstractType
{


    public function __construct(
        private readonly Security               $security,
        private readonly EntityManagerInterface $entityManager
    ) {
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title', TextType::class,
                [
                    'label' => 'Titre'
                ]
            )
            ->add(
                'content', TextareaType::class,
                [
                    'label' => 'Contenu'
                ]
            )
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $task = $event->getData();

            // If the task in not in DB yet, prefill the data with current user.
            $defaultUser = $this->entityManager->contains($task) ? $task->getAuthor() : $this->security->getUser();

            if ($this->security->isGranted('ROLE_ADMIN')) {
                $form->add(
                    'author',
                    EntityType::class,
                    [
                        'class' => User::class,
                        'label' => 'Auteur',
                        'placeholder' => 'Sélectionner un auteur',
                        'choice_label' => 'username',
                        'choice_value' => 'id',
                        'data' => $defaultUser
                    ]
                );
            }
        }
        );
    }


}
