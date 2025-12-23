<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ContactSubmissionTest extends ApiTestCase
{
    protected static ?bool $alwaysBootKernel = true;

    public function testSuccessfulCreateContactSubmission(): void
    {
        $client = static::createClient();
        $response = $client->request(
            'POST',
            '/api/contact_submissions',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'fullName' => 'Johny Bravo',
                    'email' => 'john@example.com',
                    'content'=> 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis sit amet egestas dui, nec luctus nibh. Cras lectus erat, imperdiet sit amet metus sit amet, mollis interdum neque. Sed laoreet mollis posuere. Nullam leo ante, finibus ac augue at, luctus rhoncus lorem.',
                    'consent' => true
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('id', $data);
    }

    #[Depends('testSuccessfulCreateContactSubmission')]
    public function testList(): void
    {
        $client = static::createClient();
        $response = $client->request(
            'GET',
            '/api/contact_submissions',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        $this->assertResponseIsSuccessful();

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        foreach ($data as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('fullName', $item);
            $this->assertArrayHasKey('email', $item);
            $this->assertArrayHasKey('content', $item);
            $this->assertArrayHasKey('consent', $item);
            $this->assertArrayHasKey('createdAt', $item);
        }
    }
}
