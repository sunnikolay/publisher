<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
use Symfony\Component\HttpFoundation\Response;

class SubscriberControllerTest extends AbstractControllerTest
{
    public function testSubscribe(): void
    {
        $content = json_encode(['email' => 'test@mail.com', 'agreed' => true]);
        $this->client->request('POST', '/api/v1/subscribe', [], [], [], $content);

        $this->assertResponseIsSuccessful();
    }

    public function testSubscribeNotAgreed(): void
    {
        $content = json_encode(['email' => 'test@mail.com']);
        $this->client->request('POST', '/api/v1/subscribe', [], [], [], $content);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonDocumentMatches($responseContent, [
            '$.message' => 'validation failed',
            '$.details.validations' => self::countOf(1),
            '$.details.validations[0].field' => 'agreed',
        ]);
    }
}
