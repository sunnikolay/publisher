<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;

class AuthControllerTest extends AbstractControllerTest
{
    public function testSignUp(): void
    {
        $this->client->request('POST', '/api/v1/auth/signUp', [], [], [], json_encode([
            'firstName' => 'Vasya',
            'lastName' => 'Testov',
            'email' => 'test@test.com',
            'password' => '12312312',
            'confirmPassword' => '12312312',
        ]));

        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();

        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['token', 'refresh_token'],
            'properties' => [
                'token' => ['type' => 'string'],
                'refresh_token' => ['type' => 'string'],
            ],
        ]);
    }
}
