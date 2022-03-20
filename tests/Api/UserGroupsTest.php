<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Group;
use App\Entity\User;
use App\Utils\Util;
use Faker\Factory as Faker;
use Symfony\Contracts\HttpClient\ResponseInterface;

class UserGroupsTest extends AbstractTestCase
{
    public function testUpdateUserGroup(): void
    {
        $client = static::createClient();

        $this->databaseTool->loadFixtures([]);

        $token = $this->login($client);

        $groupData = [
            'name' => 'The Cool Kids',
            'description' => 'Cum lamia velum, omnes amicitiaes desiderium fidelis, fortis scutumes.',
        ];

        $this->createNewGroup($client, $token, $groupData);

        $groupIri = $this->findIriBy(Group::class, ['name' => $groupData['name']]);

        $faker = Faker::create();
        $util = new Util();

        $userData = [
            'firstName' => $faker->firstName(),
            'lastName' => $faker->lastName(),
            'email' => $faker->email(),
            'phone' => $util->fakeNumber(),
            'age' => $faker->numberBetween(13, 55),
            'type' => 'Test #1',
        ];

        $this->createNewUser($client, $token, $userData);

        $userIri = $this->findIriBy(User::class, ['firstName' => $userData['firstName']]);

        $userId = $util->getIdFromIri($userIri);

        $userGroupIri = sprintf('/user_groups/%d', $userId);

        $client->request('PUT', $userGroupIri, [
            'json' => [
                'group' => $groupIri,
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);

        $client->request('PUT', $userGroupIri, [
            'json' => [
                'group' => $groupIri,
            ],
            'headers' => [
                'authorization' => sprintf('Bearer %s', $token)
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $userGroupIri,
            'group' => $groupIri,
        ]);
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