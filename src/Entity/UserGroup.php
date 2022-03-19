<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [],
    itemOperations: ['get', 'put'],
    attributes: [
        'denormalization_context' => ['groups' => ['user_group:write']],
        'normalization_context' => ['groups' => ['user_group:read']],
    ],
)]
class UserGroup
{
    /**
     * @ApiProperty(identifier=true)
     */
    #[Groups(['user_group:read'])]
    public $id;

    #[Groups(['user_group:write', 'user_group:read'])]
    #[Assert\NotBlank]
    public $group;
}