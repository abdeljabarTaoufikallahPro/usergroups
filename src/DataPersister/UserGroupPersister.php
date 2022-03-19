<?php

namespace App\DataPersister;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\UserGroup;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserGroupPersister implements DataPersisterInterface
{
    private EntityManagerInterface $entityManager;
    private IriConverterInterface $iriConverter;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, IriConverterInterface $iriConverter)
    {
        $this->entityManager = $entityManager;
        $this->iriConverter = $iriConverter;
        $this->userRepository = $userRepository;
    }

    public function supports($data): bool
    {
        return $data instanceof UserGroup;
    }

    public function persist($data)
    {
        $user = $this->userRepository->find($data->id);
        $user->setUserGroup( $this->iriConverter->getItemFromIri($data->group) );

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function remove($data)
    {
        throw new \Exception('Not supported method');
    }
}