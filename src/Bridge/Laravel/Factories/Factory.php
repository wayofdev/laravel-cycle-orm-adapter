<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Factories;

use Cycle\ORM\ORMInterface;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laminas\Hydrator\ReflectionHydrator;
use ReflectionClass;
use ReflectionException;

use function method_exists;

abstract class Factory extends EloquentFactory
{
    /**
     * Get the appropriate model factory for the given model name.
     */
    public static function factoryForEntity(string $modelName): EloquentFactory
    {
        /** @var EloquentFactory $factory */
        $factory = static::resolveFactoryName($modelName);

        return $factory::new();
    }

    /**
     * Get the factory name for the given model name.
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
     * @throws ReflectionException
     */
    public function create($attributes = [], Model $parent = null)
    {
        if ([] !== $attributes) {
            return $this->state($attributes)->create([], $parent);
        }

        $results = $this->make($attributes, $parent);

        if ($results instanceof Collection) {
            $this->store($results);

            $this->callAfterCreating($results, $parent);
        } else {
            $this->store(collect([$results]));

            $this->callAfterCreating(collect([$results]), $parent);
        }

        return $results;
    }

    /**
     * Create a collection of models and persist them to the database.
     */
    public function createMany(iterable $records): Collection
    {
        return new Collection(
            collect($records)->map(function ($record) {
                return $this->state($record)->create();
            })
        );
    }

    /**
     * Create a collection of models.
     *
     * @throws ReflectionException
     */
    public function make($attributes = [], Model $parent = null)
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

    /**
     * Set the connection name on the results and store them.
     */
    protected function store(Collection $results): void
    {
        $results->each(function ($entity): void {
            /** @var ORMInterface $orm */
            $orm = app(ORMInterface::class);

            $repository = $orm->getRepository($this->modelName());
            // @phpstan-ignore-next-line
            $repository->persist($entity);
        });
    }

    /**
     * Make an instance of the model with the given attributes.
     *
     * @throws ReflectionException
     */
    protected function makeInstance(?Model $parent)
    {
        $hydrator = new ReflectionHydrator();
        $object = (new ReflectionClass($this->modelName()))->newInstanceWithoutConstructor();

        return $hydrator->hydrate(
            $this->getRawAttributes($parent),
            $object
        );
    }
}
