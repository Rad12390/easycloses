<?php

namespace LocalsBest\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FeedbackControllerTest extends WebTestCase
{
    public function testIndexforproduct()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/indexForProduct');
    }

    public function testCreate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/create');
    }

}
