<?php

namespace App\Tests\Controller;

use App\Controller\BookController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{

    public function testBookByCategory()
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/category/4/books');
        $response = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonFile(
            __DIR__.'/responses/BookControllerTest_testBookByCategory.json',
            $response
        );
    }
}
