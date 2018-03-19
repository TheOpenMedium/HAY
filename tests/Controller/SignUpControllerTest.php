<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SignUpControllerTest extends WebTestCase
{
    public function testShowSignUpPage()
    {
        $client = static::createClient();

        $client->request('GET', '/en/signup');

        $response = $client->getResponse()->getContent();

        $this->assertEquals(1, preg_match('/Sign%sUp/', $response));
    }

    public function testSignUp()
    {
        // TODO: Testing SignUp
    }

    public function testShowLogInPage()
    {
        $client = static::createClient();

        $client->request('GET', '/en/login');

        $response = $client->getResponse()->getContent();

        $this->assertEquals(1, preg_match('/Log%sIn/', $response));
    }

    public function testLogIn()
    {
        // TODO: Testing LogIn
    }
}
