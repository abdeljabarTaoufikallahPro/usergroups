<?php

namespace App\Factory;

use App\Entity\Group;
use App\Repository\GroupRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Group>
 *
 * @method static Group|Proxy createOne(array $attributes = [])
 * @method static Group[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Group|Proxy find(object|array|mixed $criteria)
 * @method static Group|Proxy findOrCreate(array $attributes)
 * @method static Group|Proxy first(string $sortedField = 'id')
 * @method static Group|Proxy last(string $sortedField = 'id')
 * @method static Group|Proxy random(array $attributes = [])
 * @method static Group|Proxy randomOrCreate(array $attributes = [])
 * @method static Group[]|Proxy[] all()
 * @method static Group[]|Proxy[] findBy(array $attributes)
 * @method static Group[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Group[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static GroupRepository|RepositoryProxy repository()
 * @method Group|Proxy create(array|callable $attributes = [])
 */
final class GroupFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->streetName(),
            'description' => self::faker()->text(300),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Group $group): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Group::class;
    }
}
