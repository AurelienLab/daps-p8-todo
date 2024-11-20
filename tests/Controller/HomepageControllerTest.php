<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomepageControllerTest extends WebTestCase
{

    public function testHomepage()
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');
        $url = $router->generate('homepage');
        $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}