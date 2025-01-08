<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{


    use ReloadDatabaseTrait;

    private KernelBrowser $client;
    private null|object $router;


    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->router = static::$kernel->getContainer()->get('router');
        $this->userRepository = $this->getContainer()->get(UserRepository::class);
    }


    public function testListTaskPage()
    {
        $url = $this->router->generate('task_list');
        $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testListTaskDisplayTask()
    {
        $url = $this->router->generate('task_list');
        $this->client->request(Request::METHOD_GET, $url);
        $this->assertSelectorTextContains('.thumbnail', 'Test Task Undone');
    }


    public function testCreateTaskPage()
    {
        $user = $this->userRepository->findOneByEmail('john@doe.com');
        $this->client->loginUser($user);

        $url = $this->router->generate('task_create');
        $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testCreateTaskUnauthenticated()
    {
        $url = $this->router->generate('task_create');
        $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertRouteSame('login');
    }


    public function testCreateTask()
    {
        $user = $this->userRepository->findOneByEmail('john@doe.com');
        $this->client->loginUser($user);

        $url = $this->router->generate('task_create');
        $crawler = $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Ajouter')->form(
            [
                'task[title]' => 'New Task',
                'task[content]' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            ]
        );

        $this->client->submit($form);
        $this->assertResponseRedirects();
        $crawler = $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('div.alert.alert-success', 'Superbe ! La tâche a été bien été ajoutée.');
        $this->assertCount(4, $crawler->filter('.thumbnail'));

        $taskRepository = $this->getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['title' => 'New Task']);
        $this->assertNotNull($task);
        $this->assertEquals($task->getAuthor()->getId(), $user->getId());
    }


    public function testCreateTaskEmptyTitle()
    {
        $user = $this->userRepository->findOneByEmail('john@doe.com');
        $this->client->loginUser($user);

        $url = $this->router->generate('task_create');
        $crawler = $this->client->request(Request::METHOD_GET, $url);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(
            [
                'task[title]' => '',
                'task[content]' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            ]
        );

        $this->client->submit($form);
        $this->assertSelectorTextContains('li', 'Vous devez saisir un titre.');
    }


    public function testCreateTaskEmptyContent()
    {
        $user = $this->userRepository->findOneByEmail('john@doe.com');
        $this->client->loginUser($user);

        $url = $this->router->generate('task_create');
        $crawler = $this->client->request(Request::METHOD_GET, $url);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(
            [
                'task[title]' => 'Task title',
                'task[content]' => '',
            ]
        );

        $this->client->submit($form);
        $this->assertSelectorTextContains('li', 'Vous devez saisir du contenu.');
    }


    public function testMarkTaskDone()
    {

        $url = $this->router->generate('task_list');
        $crawler = $this->client->request(Request::METHOD_GET, $url);

        $form = $crawler->selectButton('Marquer comme faite')->form();

        $this->client->submit($form);
        $this->assertResponseRedirects();
        $crawler = $this->client->followRedirect();

        $this->assertSelectorTextContains('div.alert.alert-success', 'Superbe ! La tâche Test Task Undone a bien été marquée comme faite.');
        $this->assertCount(2, $crawler->filter('.glyphicon-ok'));
        $this->assertCount(1, $crawler->filter('.glyphicon-remove'));
    }


    public function testMarkTaskUndone()
    {

        $url = $this->router->generate('task_list');
        $crawler = $this->client->request(Request::METHOD_GET, $url);

        $form = $crawler->selectButton('Marquer non terminée')->form();

        $this->client->submit($form);
        $this->assertResponseRedirects();
        $crawler = $this->client->followRedirect();

        $this->assertSelectorTextContains('div.alert.alert-success', 'Superbe ! La tâche Test Task Done a bien été marquée comme faite.');
        $this->assertCount(0, $crawler->filter('.glyphicon-ok'));
        $this->assertCount(3, $crawler->filter('.glyphicon-remove'));
    }


    public function testDeleteTaskUnauthenticated()
    {
        $url = $this->router->generate('task_list');
        $this->client->request(Request::METHOD_GET, $url);

        $this->assertSelectorNotExists('form > button.btn.btn-danger');

        $taskRepository = $this->getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneBy([]);

        $urlDelete = $this->router->generate('task_delete', ['id' => $task->getId()]);
        $this->client->request(Request::METHOD_GET, $urlDelete);
        $this->client->followRedirect();
        $this->assertRouteSame('login');

    }


    public function testDeleteAnonymousTaskByUser()
    {
        $user = $this->userRepository->findOneByEmail('john@doe.com');
        $this->client->loginUser($user);

        $url = $this->router->generate('task_list');
        $this->client->request(Request::METHOD_GET, $url);

        $this->assertSelectorCount(1, 'form > button.btn.btn-danger');

        $taskRepository = $this->getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => null]);

        $urlDelete = $this->router->generate('task_delete', ['id' => $task->getId()]);
        $this->client->request(Request::METHOD_GET, $urlDelete);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }


    public function testDeleteAnonymousTaskByAdmin()
    {
        $admin = $this->userRepository->findOneByEmail('admin@doe.com');
        $this->client->loginUser($admin);

        $url = $this->router->generate('task_list');
        $this->client->request(Request::METHOD_GET, $url);

        $this->assertSelectorCount(3, 'form > button.btn.btn-danger');

        $taskRepository = $this->getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => null]);

        $urlDelete = $this->router->generate('task_delete', ['id' => $task->getId()]);
        $this->client->request(Request::METHOD_GET, $urlDelete);

        $this->client->followRedirect();
        $this->assertRouteSame('task_list');

    }


    public function testDeleteTaskByAuthor()
    {
        $user = $this->userRepository->findOneByEmail('john@doe.com');
        $this->client->loginUser($user);

        $url = $this->router->generate('task_list');
        $this->client->request(Request::METHOD_GET, $url);
        $this->assertSelectorCount(1, 'form > button.btn.btn-danger');

        $taskRepository = $this->getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => $user]);

        $urlDelete = $this->router->generate('task_delete', ['id' => $task->getId()]);
        $this->client->request(Request::METHOD_GET, $urlDelete);

        $this->client->followRedirect();
        $this->assertRouteSame('task_list');

    }


    public function testDeleteTaskByOtherUser()
    {
        $user2 = $this->userRepository->findOneByEmail('franck@doe.com');
        $this->client->loginUser($user2);

        $user = $this->userRepository->findOneByEmail('john@doe.com');

        $url = $this->router->generate('task_list');
        $this->client->request(Request::METHOD_GET, $url);
        $this->assertSelectorCount(0, 'form > button.btn.btn-danger');

        $taskRepository = $this->getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => $user]);

        $urlDelete = $this->router->generate('task_delete', ['id' => $task->getId()]);
        $this->client->request(Request::METHOD_GET, $urlDelete);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }


    public function testEditTaskUnauthenticated()
    {
        $taskRepository = $this->getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneBy([]);

        $url = $this->router->generate('task_edit', ['id' => $task->getId()]);
        $this->client->request(Request::METHOD_GET, $url);
        $this->client->followRedirect();
        $this->assertRouteSame('login');
    }


    public function testEditAnonymousTaskByUser()
    {
        $user = $this->userRepository->findOneByEmail('john@doe.com');
        $this->client->loginUser($user);

        $taskRepository = $this->getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => null]);

        $url = $this->router->generate('task_edit', ['id' => $task->getId()]);
        $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }


    public function testEditAnonymousTaskByAdmin()
    {
        $admin = $this->userRepository->findOneByEmail('admin@doe.com');
        $this->client->loginUser($admin);

        $taskRepository = $this->getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => null]);

        $url = $this->router->generate('task_edit', ['id' => $task->getId()]);
        $crawler = $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Modifier')->form(
            [
                'task[title]' => 'Test Task update',
                'task[content]' => 'Test Task update content',
            ]
        );
        $this->client->submit($form);

        $this->client->followRedirect();
        $this->assertRouteSame('task_list');
        $this->assertSelectorTextContains('div.alert.alert-success', 'Superbe ! La tâche a bien été modifiée.');
    }


    public function testEditUserTaskByAdmin()
    {
        $user = $this->userRepository->findOneByEmail('john@doe.com');

        $admin = $this->userRepository->findOneByEmail('admin@doe.com');
        $this->client->loginUser($admin);

        $taskRepository = $this->getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => $user]);

        $url = $this->router->generate('task_edit', ['id' => $task->getId()]);
        $crawler = $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Modifier')->form(
            [
                'task[title]' => 'Test Task update',
                'task[content]' => 'Test Task update content',
            ]
        );
        $this->client->submit($form);

        $this->client->followRedirect();
        $this->assertRouteSame('task_list');
        $this->assertSelectorTextContains('div.alert.alert-success', 'Superbe ! La tâche a bien été modifiée.');
    }


    public function testEditUserTaskByAuthor()
    {
        $user = $this->userRepository->findOneByEmail('john@doe.com');
        $this->client->loginUser($user);

        $taskRepository = $this->getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => $user]);

        $url = $this->router->generate('task_edit', ['id' => $task->getId()]);
        $crawler = $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Modifier')->form(
            [
                'task[title]' => 'Test Task update',
                'task[content]' => 'Test Task update content',
            ]
        );
        $this->client->submit($form);

        $this->client->followRedirect();
        $this->assertRouteSame('task_list');
        $this->assertSelectorTextContains('div.alert.alert-success', 'Superbe ! La tâche a bien été modifiée.');
    }


    public function testEditUserTaskByOtherUser()
    {
        $user1 = $this->userRepository->findOneByEmail('john@doe.com');

        $user2 = $this->userRepository->findOneByEmail('franck@doe.com');
        $this->client->loginUser($user2);

        $taskRepository = $this->getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['author' => $user1]);

        $url = $this->router->generate('task_edit', ['id' => $task->getId()]);
        $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }


}