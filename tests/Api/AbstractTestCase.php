<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

abstract class AbstractTestCase extends ApiTestCase
{
    /**
     * @var AbstractDatabaseTool
     */
    protected $databaseTool;

    public function setUp(): void
    {
        parent::setUp();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    protected function login(Client $client): ?string
    {
        $response = $client->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'admin',
                'password' => 'password',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);

        $data = json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR);

        return $data->token;
    }

    protected function createResourceAndGetIri(Client $client, string $endpoint, array $payload): string
    {
        $resource = $this->createResource($client, $endpoint, $payload);

        return $resource['@id'];
    }

    protected function createResource(Client $client, string $endpoint, array $payload): array
    {
        $response = $client->request('POST', $endpoint, [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => $payload,
        ]);
        self::assertResponseIsSuccessful();

        return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    protected function loadFixtures($fixtures, PurgeMode $purgeMode = null): array
    {
        $loader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        return $loader->load(
            $fixtures,
            [],
            [],
            $purgeMode ?? PurgeMode::createNoPurgeMode()
        );
    }
}