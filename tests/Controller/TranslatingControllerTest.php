<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TranslatingControllerTest extends WebTestCase
{
    public function testEnglishTranslation()
    {
        $client = static::createClient();

        $client->request('GET', '/en/');

        $response = $client->getResponse()->getContent();

        $this->assertEquals(1, preg_match('/Write/', $response));
    }

    public function testFrenchTranslation()
    {
        $client = static::createClient();

        $client->request('GET', '/en/');

        $response = $client->getResponse()->getContent();

        $this->assertEquals(1, preg_match('/Écrire/', $response));
    }
}
