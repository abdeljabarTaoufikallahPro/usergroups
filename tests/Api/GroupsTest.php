<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\DataFixtures\AppFixtures;
use App\Entity\Group;
use Symfony\Contracts\HttpClient\ResponseInterface;

class GroupsTest extends AbstractTestCase
{
    public function testGetCollection(): void
    {
        $client = $this->createClient();

        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);

        $response =$client->request('GET', '/groups');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertCount(30, $response->toArray()['hydra:member']);

        $this->assertMatchesResourceCollectionJsonSchema(Group::class);
    }

    public function testCreateGroup(): void
    {
        $client = static::createClient();
        $token = $this->login($client);

        $client->request('POST', '/groups', [
            'json' => [],
        ]);

        $this->assertResponseStatusCodeSame(401);

        $data = [
            'name' => 'The Cool Kids',
            'description' => 'Cum lamia velum, omnes amicitiaes desiderium fidelis, fortis scutumes.',
        ];

        $response = $this->createNewGroup($client, $token, $data);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/contexts/Group',
            '@type' => 'Group',
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        $this->assertMatchesRegularExpression('~^/groups/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Group::class);
    }

    public function testCreateInvalidGroup(): void
    {
        $client = static::createClient();
        $token = $this->login($client);

        $client->request('POST', '/groups', [
            'json' => [
                'name' => '',
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

    public function testUpdateGroup(): void
    {
        $client = static::createClient();

        $this->databaseTool->loadFixtures([]);

        $token = $this->login($client);

        $data = [
            'name' => 'The Cool Kids',
            'description' => 'Cum lamia velum, omnes amicitiaes desiderium fidelis, fortis scutumes.',
        ];
        $this->createNewGroup($client, $token, $data);

        $iri = $this->findIriBy(Group::class, ['name' => 'The Cool Kids']);

        $client->request('PUT', $iri, [
            'json' => [
                'name' => 'The Cool Kids Updated',
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);

        $client->request('PUT', $iri, [
            'json' => [
                'name' => 'The Cool Kids Updated',
            ],
            'headers' => [
                'authorization' => sprintf('Bearer %s', $token)
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'The Cool Kids Updated',
        ]);
    }

    public function testDeleteGroup(): void
    {
        $client = static::createClient();

        $this->databaseTool->loadFixtures([]);

        $token = $this->login($client);

        $data = [
            'name' => 'The Cool Kids',
            'description' => 'Cum lamia velum, omnes amicitiaes desiderium fidelis, fortis scutumes.',
        ];
        $this->createNewGroup($client, $token, $data);

        $iri = $this->findIriBy(Group::class, ['name' => $data['name']]);

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
    private function createNewGroup(Client $client, string $token, array $data): ResponseInterface
    {
        $response = $client->request('POST', '/groups', [
            'json' => [
                'name' => $data['name'],
                'description' => $data['description'],
            ],
            'headers' => [
                'authorization' => sprintf('Bearer %s', $token)
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

        return $response;
    }
}