<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{


    use ReloadDatabaseTrait;

    public function testLoginPage()
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');
        $url = $router->generate('login');
        $client->request('GET', $url);
        $this->assertResponseStatusCodeSame(200);
    }


    public function testLogin()
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');
        $url = $router->generate('login');
        $crawler = $client->request('GET', $url);

        $form = $crawler->selectButton('Se connecter')->form(
            [
                '_username' => 'john',
                '_password' => 'azerty123'
            ]
        );

        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();


        $this->assertResponseStatusCodeSame(200);
    }


    public function testLogout()
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');
        $url = $router->generate('logout');

        $userRepository = $this->getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('john@doe.com');
        $client->loginUser($testUser);

        $client->request('GET', $url);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
    }


}