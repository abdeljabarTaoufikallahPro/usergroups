<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\DataFixtures\AppFixtures;
use App\Entity\User;
use App\Utils\Util;
use Faker\Factory as Faker;
use Symfony\Contracts\HttpClient\ResponseInterface;

class UsersTest extends AbstractTestCase
{
    public function testGetCollection(): void
    {
        $client = $this->createClient();

        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);

        $response =$client->request('GET', '/users');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertCount(30, $response->toArray()['hydra:member']);

        $this->assertMatchesResourceCollectionJsonSchema(User::class);
    }

    public function testCreateUser(): void
    {
        $client = static::createClient();

        $client->request('POST', '/users', [
            'json' => [],
        ]);

        $this->assertResponseStatusCodeSame(401);

        $faker = Faker::create();
        $util = new Util();

        $data = [
            'firstName' => $faker->firstName(),
            'lastName' => $faker->lastName(),
            'email' => $faker->email(),
            'phone' => $util->fakeNumber(),
            'age' => $faker->numberBetween(13,55),
            'type' => 'Test #3',
        ];

        $token = $this->login($client);

        $response = $this->createNewUser($client, $token, $data);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@type' => 'User',
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'age' => $data['age'],
            'type' => $data['type'],
        ]);

        $this->assertMatchesRegularExpression('~^/users/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(User::class);
    }

    public function testCreateInvalidUser(): void
    {
        $client = static::createClient();
        $token = $this->login($client);

        $client->request('POST', '/users', [
            'json' => [
                'firstName' => '',
            ],
            'headers' => [
                'authorization' => sprintf('Bearer %s', $token)
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
        ]);
    }

    public function testUpdateUser(): void
    {
        $client = static::createClient();

        $this->databaseTool->loadFixtures([]);

        $token = $this->login($client);

        $faker = Faker::create();
        $util = new Util();

        $data = [
            'firstName' => $faker->firstName(),
            'lastName' => $faker->lastName(),
            'email' => $faker->email(),
            'phone' => $util->fakeNumber(),
            'age' => $faker->numberBetween(13,55),
            'type' => 'Test #2',
        ];

        $this->createNewUser($client, $token, $data);

        $iri = $this->findIriBy(User::class, ['firstName' => $data['firstName']]);

        $client->request('PUT', $iri, [
            'json' => [
                'firstName' => 'Abdeljabar',
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);

        $client->request('PUT', $iri, [
            'json' => [
                'firstName' => 'Abdeljabar',
            ],
            'headers' => [
                'authorization' => sprintf('Bearer %s', $token)
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'firstName' => 'Abdeljabar',
        ]);
    }

    public function testDeleteUser(): void
    {
        $client = static::createClient();

        $this->databaseTool->loadFixtures([]);

        $token = $this->login($client);

        $faker = Faker::create();
        $util = new Util();

        $data = [
            'firstName' => $faker->firstName(),
            'lastName' => $faker->lastName(),
            'email' => $faker->email(),
            'phone' => $util->fakeNumber(),
            'age' => $faker->numberBetween(13,55),
            'type' => 'Test #1',
        ];

        $this->createNewUser($client, $token, $data);

        $iri = $this->findIriBy(User::class, ['firstName' => $data['firstName']]);

        $client->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(401);

        $client->request('DELETE', $iri, [
            'headers' => [
                'authorization' => sprintf('Bearer %s', $token)
            ]
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    /**
     * @param Client $client
     * @param string $token
     * @param array $data
     * @return \Symfony\Contracts\HttpClient\ResponseInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function createNewUser(Client $client, string $token, array $data): ResponseInterface
    {
        $response = $client->request('POST', '/users', [
            'json' => [
                'firstName' => $data['firstName'],
                'lastName' => $data['lastName'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'age' => $data['age'],
                'type' => $data['type'],
            ],
            'headers' => [
                'authorization' => sprintf('Bearer %s', $token)
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

        return $response;
    }
}