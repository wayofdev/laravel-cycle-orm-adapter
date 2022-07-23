<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Factories;

use Cycle\ORM\ORMInterface;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laminas\Hydrator\ReflectionHydrator;
use ReflectionClass;
use ReflectionException;
use WayOfDev\Cycle\Repository;

use function array_map;
use function method_exists;

/**
 * @template TModel of Model
 */
abstract class Factory extends EloquentFactory
{
    public static function factoryForEntity(string $entityClass): EloquentFactory
    {
        return parent::factoryForModel($entityClass);
    }

    /**
     * @return class-string<EloquentFactory>
     */
    public static function resolveFactoryName(string $modelName): string
    {
        if (method_exists($modelName, 'resolveFactoryName')) {
            return $modelName::resolveFactoryName();
        }

        return parent::resolveFactoryName($modelName);
    }

    /**
     * Create a collection of models and persist them to the database.
     *
     * @param (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed> $attributes
     *
     * @throws ReflectionException
     *
     * @return Collection<int, Model|TModel>|Model|TModel
     */
    public function create($attributes = [], ?Model $parent = null)
    {
        if ([] !== $attributes) {
            return $this->state($attributes)->create([], $parent);
        }

        $results = $this->make($attributes, $parent);

        if ($results instanceof Collection) {
            $this->store($results);

            $this->callAfterCreating($results, $parent);
        } else {
            $this->store(Collection::make([$results]));

            $this->callAfterCreating(collect([$results]), $parent);
        }

        return $results;
    }

    /**
     * @throws ReflectionException
     */
    public function createMany(iterable $records): Collection
    {
        return new Collection(
            array_map(function ($record) {
                return $this->state($record)->create();
            }, (array) $records)
        );
    }

    /**
     * Create a collection of models.
     *
     * @param (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed> $attributes
     *
     * @throws ReflectionException
     *
     * @return Collection<int, Model|TModel>|Model|TModel
     */
    public function make($attributes = [], ?Model $parent = null)
    {
        if ([] !== $attributes) {
            return $this->state($attributes)->make([], $parent);
        }

        if (null === $this->count) {
            return tap($this->makeInstance($parent), function ($instance): void {
                $this->callAfterMaking(collect([$instance]));
            });
        }

        if (1 > $this->count) {
            $entityClass = $this->modelName();

            return Collection::make([new $entityClass()]);
        }

        $instances = Collection::times($this->count, function () use ($parent): object {
            return $this->makeInstance($parent);
        });

        $this->callAfterMaking($instances);

        return $instances;
    }

    protected function store(Collection $results): void
    {
        $results->map(function ($entity) {
            /** @var ORMInterface $orm */
            $orm = app(ORMInterface::class);

            /** @var Repository $repository */
            $repository = $orm->getRepository($this->modelName());
            $repository->persist($entity);

            return $entity;
        });
    }

    /**
     * @throws ReflectionException
     */
    protected function makeInstance(?Model $parent): object
    {
        $hydrator = new ReflectionHydrator();
        $object = (new ReflectionClass($this->modelName()))->newInstanceWithoutConstructor();

        return $hydrator->hydrate(
            $this->getRawAttributes($parent),
            $object
        );
    }
}
