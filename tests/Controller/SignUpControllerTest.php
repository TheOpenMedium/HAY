<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SignUpControllerTest extends WebTestCase
{
    public function testShowSignUpPage()
    {
        $client = static::createClient();

        $client->request('GET', '/en/signup');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testSignUp()
    {
        // TODO: Testing SignUp
    }

    public function testShowLogInPage()
    {
        $client = static::createClient();

        $client->request('GET', '/en/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testLogIn()
    {
        // TODO: Testing LogIn
    }
}
