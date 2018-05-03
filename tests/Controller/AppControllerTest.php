<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests related to the application
 *
 * List of tests:
 * * testLocaleChoosePage() -- app_locale
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
     * Test the rendering of the language choose page
     */
    public function testLocaleEnglishRedirect()
    {
        // We define a random 'Accept Language' value
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'This,Locale;q=0.9,Has;q=0.8,OneChance;q=0.7,ToBe;q=0.6,Valid;q=0.5,en;q=0.4';

        $client = static::createClient();

        // We request the app_locale controller.
        $client->request('GET', '/');

        // We verify that we have a 200 status code.
        // If the client was redirected, the status code is 302, and we don't want that.
        $this->assertTrue($client->getResponse()->headers->contains(
            'Location', '/en/'
        ));
    }

    /**
     * Test the rendering of the language choose page
     */
    public function testLocaleFrenchRedirect()
    {
        // We define a random 'Accept Language' value
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'This,Locale;q=0.9,Has;q=0.8,OneChance;q=0.7,ToBe;q=0.6,Valid;q=0.5,fr;q=0.4';

        $client = static::createClient();

        // We request the app_locale controller.
        $client->request('GET', '/');

        // We verify that we have a 200 status code.
        // If the client was redirected, the status code is 302, and we don't want that.
        $this->assertTrue($client->getResponse()->headers->contains(
            'Location', '/fr/'
        ));
    }
}
