<?php

namespace LocalsBest\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PaymentControllerTest extends WebTestCase
{
    public function testCheckout()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/checkout');
    }

    public function testCharge()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/charge');
    }

}
