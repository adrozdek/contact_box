<?php

namespace ContactBoxBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FirstControllerTest extends WebTestCase
{
    public function testRedirecttologin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
    }

}
