<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{


    use ReloadDatabaseTrait;

    private KernelBrowser $client;
    private UserRepository $userRepository;
    private null|object $router;


    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->router = static::$kernel->getContainer()->get('router');
        $this->userRepository = $this->getContainer()->get(UserRepository::class);
    }


    public function testListUserPageUnauthenticated()
    {
        $url = $this->router->generate('user_list');
        $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseRedirects('/login');
    }


    public function testListUserPageAuthenticated()
    {
        $admin = $this->userRepository->findOneByEmail('admin@doe.com');
        $this->client->loginUser($admin);

        $url = $this->router->generate('user_list');
        $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testCreateUserPageUnauthenticated(): void
    {
        $url = $this->router->generate('user_create');
        $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseRedirects('/login');
    }


    public function testCreateUserPageAuthenticated(): void
    {
        $admin = $this->userRepository->findOneByEmail('admin@doe.com');
        $this->client->loginUser($admin);

        $url = $this->router->generate('user_create');
        $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseIsSuccessful();
    }


    public function testCreateUser()
    {
        $admin = $this->userRepository->findOneByEmail('admin@doe.com');
        $this->client->loginUser($admin);

        $url = $this->router->generate('user_create');
        $crawler = $this->client->request(Request::METHOD_GET, $url);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(
            [
                'user[username]' => 'Test',
                'user[password][first]' => 'azerty123',
                'user[password][second]' => 'azerty123',
                'user[roles]' => 'ROLE_USER',
                'user[email]' => 'test@test.com',
            ]
        );

        $this->client->submit($form);
        $this->assertResponseRedirects();
        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('div.alert.alert-success', 'Superbe ! L\'utilisateur a bien été ajouté.');
    }


    public function testCreateUserUsernameExists()
    {
        $admin = $this->userRepository->findOneByEmail('admin@doe.com');
        $this->client->loginUser($admin);

        $url = $this->router->generate('user_create');
        $crawler = $this->client->request(Request::METHOD_GET, $url);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(
            [
                'user[username]' => 'john',
                'user[password][first]' => '123456',
                'user[password][second]' => '123456',
                'user[roles]' => 'ROLE_USER',
                'user[email]' => 'john@test.com',
            ]
        );

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(500);
    }


    public function testCreateUserEmailExists()
    {
        $admin = $this->userRepository->findOneByEmail('admin@doe.com');
        $this->client->loginUser($admin);

        $url = $this->router->generate('user_create');
        $crawler = $this->client->request(Request::METHOD_GET, $url);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(
            [
                'user[username]' => 'Michel',
                'user[password][first]' => '123456',
                'user[password][second]' => '123456',
                'user[roles]' => 'ROLE_USER',
                'user[email]' => 'john@doe.com',
            ]
        );

        $this->client->submit($form);
        $this->assertSelectorTextContains('div.invalid-feedback', 'This value is already used.');
    }


    public function testEditUserPageUnauthenticated(): void
    {
        $userToEdit = $this->userRepository->findOneByEmail('john@doe.com');
        $url = $this->router->generate('user_edit', ['id' => $userToEdit->getId()]);
        $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseRedirects('/login');
    }


    public function testEditUserPageNonAdmin(): void
    {
        $user = $this->userRepository->findOneByEmail('john@doe.com');
        $this->client->loginUser($user);

        $userToEdit = $this->userRepository->findOneByEmail('john@doe.com');
        $url = $this->router->generate('user_edit', ['id' => $userToEdit->getId()]);
        $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }


    public function testEditUserPageAdmin(): void
    {
        $admin = $this->userRepository->findOneByEmail('admin@doe.com');
        $this->client->loginUser($admin);

        $userToEdit = $this->userRepository->findOneByEmail('john@doe.com');
        $url = $this->router->generate('user_edit', ['id' => $userToEdit->getId()]);
        $this->client->request(Request::METHOD_GET, $url);
        $this->assertResponseIsSuccessful();
    }


    public function testEditUser(): void
    {
        $admin = $this->userRepository->findOneByEmail('admin@doe.com');
        $this->client->loginUser($admin);

        $userToEdit = $this->userRepository->findOneByEmail('john@doe.com');
        $url = $this->router->generate('user_edit', ['id' => $userToEdit->getId()]);
        $crawler = $this->client->request(Request::METHOD_GET, $url);

        $form = $crawler->selectButton('Modifier')->form(
            [
                'user[username]' => 'john-new',
            ]
        );

        $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('div.alert.alert-success', 'Superbe ! L\'utilisateur a bien été modifié.');
        $this->assertSelectorCount(3, 'tbody tr');
        $this->assertAnySelectorTextContains('td', 'john-new');
    }


}