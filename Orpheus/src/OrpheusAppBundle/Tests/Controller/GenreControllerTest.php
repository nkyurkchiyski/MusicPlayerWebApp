<?php

namespace OrpheusAppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GenreControllerTest extends WebTestCase
{
    public function testAll()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/genres/all');
    }

    public function testDetails()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/genres');
    }

}
