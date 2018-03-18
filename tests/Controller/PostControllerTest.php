<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testShowPostPage()
    {
        $client = static::createClient();

        $client->request('GET', '/en/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShowPost()
    {
        // TODO: Testing Showing Post
    }

    public function testShowComment()
    {
        // TODO: Testing Showing Comment
    }

    public function testSubmitPost()
    {
        // TODO: Testing Submit Post
    }

    public function testSubmitComment()
    {
        // TODO: Testing Submit Comment
    }
}
