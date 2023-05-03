<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Factories;

use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;

use function array_map;
use function method_exists;
use function range;

abstract class Factory extends EloquentFactory
{
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
     * @param mixed $attributes
     */
    public function create($attributes = [], ?Model $parent = null)
    {
        if ([] !== $attributes) {
            return $this->state($attributes)->create([], $parent);
        }

        $results = $this->make($attributes, $parent);

        if ($results instanceof Model) {
            $this->store(collect([$results]));

            $this->callAfterCreating(collect([$results]), $parent);
        } else {
            $this->store($results);

            $this->callAfterCreating($results, $parent);
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
     * @param mixed $attributes
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
            return $this->newModel()->newCollection();
        }

        $instances = $this->newModel()->newCollection(array_map(function () use ($parent) {
            return $this->makeInstance($parent);
        }, range(1, $this->count)));

        $this->callAfterMaking($instances);

        return $instances;
    }

    /**
     * Set the connection name on the results and store them.
     */
    protected function store(Collection $results): void
    {
        $results->each(function ($model): void {
            if (! isset($this->connection)) {
                $model->setConnection($model->newQueryWithoutScopes()->getConnection()->getName());
            }

            $model->save();

            foreach ($model->getRelations() as $name => $items) {
                if ($items instanceof Enumerable && $items->isEmpty()) {
                    $model->unsetRelation($name);
                }
            }

            $this->createChildren($model);
        });
    }

    /**
     * Make an instance of the model with the given attributes.
     */
    protected function makeInstance(?Model $parent)
    {
        return Model::unguarded(function () use ($parent) {
            return tap($this->newModel($this->getExpandedAttributes($parent)), function ($instance): void {
                if (isset($this->connection)) {
                    $instance->setConnection($this->connection);
                }
            });
        });
    }
}
