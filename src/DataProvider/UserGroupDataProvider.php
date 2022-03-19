<?php

namespace App\DataProvider;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\UserGroup;
use App\Repository\UserRepository;

class UserGroupDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private UserRepository $userRepository;
    private IriConverterInterface $iriConverter;

    public function __construct(UserRepository $userRepository, IriConverterInterface $iriConverter)
    {
        $this->userRepository = $userRepository;
        $this->iriConverter = $iriConverter;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): UserGroup|null
    {
        $user = $this->userRepository->find($id);

        $userGroup = new UserGroup();
        $userGroup->id = $id;
        $userGroup->group = $user->getUserGroup() ? $this->iriConverter->getIriFromItem($user->getUserGroup()) : null;

        return $userGroup;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === UserGroup::class;
    }
}