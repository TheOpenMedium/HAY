<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests related to the application
 *
 * List of tests:
 * * testLocaleChoosePage()      -- app_locale
 * * testLocaleEnglishRedirect() -- app_locale
 * * testLocaleFrenchRedirect()  -- app_locale
 * * testIndexPage()             -- app_index
 */
class AppControllerTest extends WebTestCase
{
    // TESTING: app_locale

    /**
     * Test the rendering of the language choose page
     */
    public function testLocaleChoosePage()
    {
        // We define a random 'Accept Language' value
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'This,Locale;q=0.9,Has;q=0.8,NoChance;q=0.7,ToBe;q=0.6,Valid;q=0.5';

        $client = static::createClient();

        // We request the app_locale controller.
        $client->request('GET', '/');

        // We verify that we have a 200 status code.
        // If the client was redirected, the status code is 302, and we don't want that.
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Test the redirection of the english language
     */
    public function testLocaleEnglishRedirect()
    {
        // We define a random 'Accept Language' value
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'This,Locale;q=0.9,Has;q=0.8,OneChance;q=0.7,ToBe;q=0.6,Valid;q=0.5,en;q=0.4';

        $client = static::createClient();

        // We request the app_locale controller.
        $client->request('GET', '/');

        // We verify if the user will be redirected to english pages.
        $this->assertTrue($client->getResponse()->headers->contains(
            'Location', '/en/'
        ));
    }

    /**
     * Test the redirection of the french language
     */
    public function testLocaleFrenchRedirect()
    {
        // We define a random 'Accept Language' value
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'This,Locale;q=0.9,Has;q=0.8,OneChance;q=0.7,ToBe;q=0.6,Valid;q=0.5,fr;q=0.4';

        $client = static::createClient();

        // We request the app_locale controller.
        $client->request('GET', '/');

        // We verify if the user will be redirected to french pages.
        $this->assertTrue($client->getResponse()->headers->contains(
            'Location', '/fr/'
        ));
    }

    // TESTING: app_index

    /**
     * Test the rendering of the index page
     */
    public function testIndexPage()
    {
        $client = static::createClient();

        // We request the app_index controller.
        $client->request('GET', '/en/');

        // We verify that we have a 200 status code.
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Test the submitting of posts
     */
    /*
    public function testPostSubmit()
    {
        $client = static::createClient();

        $repository = $this->getDoctrine()->getRepository(User::class);

        if (!$this->getUser()) {
            if (!$repository->find(1)) {
                $user = new User;

                $user->setFirstName('root');
                $user->setLastName('root');
                $user->setUsername('root');
                $user->setEmail('root@root.root');
                $user->setPassword(password_hash('root', PASSWORD_ARGON2I));

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }

            $loginpage = $client->request('GET', '/en/login');
            $form = $loginpage->selectButton('submit')->form();
            $form['username'] = 'root';
            $form['password'] = 'root';
            $client->submit($form);
        }

        // We request the app_index controller.
        $crawler = $client->request('GET', '/en/');

        // We verify that we have a 200 status code.
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    */
}
