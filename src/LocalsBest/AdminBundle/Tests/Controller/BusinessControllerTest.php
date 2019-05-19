<?php

namespace LocalsBest\AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BusinessControllerTest extends WebTestCase
{
    public function testSetplan()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/businesses/set-plan');
    }

}
