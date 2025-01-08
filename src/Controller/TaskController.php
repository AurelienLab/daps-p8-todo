<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskController extends AbstractController
{


    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }


    #[Route('/task', name: 'task_list')]
    public function listAction()
    {
        return $this->render('task/list.html.twig', ['tasks' => $this->entityManager->getRepository(Task::class)->findAll()]);
    }


    #[Route('/task/create', name: 'task_create')]
    #[IsGranted('ROLE_USER')]
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setAuthor($this->getUser());

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }


    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function editAction(Task $task, Request $request)
    {
        $this->denyAccessUnlessGranted('TASK_EDIT', $task);
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render(
            'task/edit.html.twig', [
                                     'form' => $form->createView(),
                                     'task' => $task,
                                 ]
        );
    }


    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTaskAction(Task $task)
    {
        $task->toggle(!$task->isDone());
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }


    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTaskAction(Task $task)
    {
        $this->denyAccessUnlessGranted('TASK_DELETE', $task);

//        $this->entityManager->remove($task);
//        $this->entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }


}
