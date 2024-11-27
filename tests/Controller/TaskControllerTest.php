<?php

namespace App\Tests\Controller;

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
        $this->assertSelectorTextContains('.thumbnail a', 'Test Task Undone');
    }


    public function testCreateTaskPage()
    {
        $url = $this->router->generate('task_create');
        $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testCreateTask()
    {
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
        $this->assertCount(3, $crawler->filter('.thumbnail'));
    }


    public function testCreateTaskEmptyTitle()
    {

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
        $this->assertCount(0, $crawler->filter('.glyphicon-remove'));
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
        $this->assertCount(2, $crawler->filter('.glyphicon-remove'));
    }


    public function testDeleteTask()
    {
        $url = $this->router->generate('task_list');
        $crawler = $this->client->request(Request::METHOD_GET, $url);
        $this->assertCount(2, $crawler->filter('.thumbnail'));

        $form = $crawler->selectButton('Supprimer')->form();
        $this->client->submit($form);
        $this->assertResponseRedirects();
        $crawler = $this->client->followRedirect();

        $this->assertSelectorTextContains('div.alert.alert-success', 'Superbe ! La tâche a bien été supprimée.');
        $this->assertCount(1, $crawler->filter('.glyphicon-ok'));
    }


}