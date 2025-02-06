<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{


    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {

    }


    #[Route('/users', name: 'user_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function listAction()
    {
        return $this->render('user/list.html.twig', ['users' => $this->entityManager->getRepository(User::class)->findAll()]);

    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/create', name: 'user_create')]
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->userPasswordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles([$form->get('roles')->getData()]);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);

    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/{id}/edit', name: 'user_edit')]
    public function editAction(User $user, Request $request)
    {
        $form = $this->createForm(UserType::class, $user);

        $originalHashedPassword = $user->getPassword();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!empty($form->get('password')?->getData())) {
                $password = $this->userPasswordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($password);
            } else {
                $user->setPassword($originalHashedPassword);
            }

            $user->setRoles([$form->get('roles')->getData()]);

            $this->entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);

    }


}
