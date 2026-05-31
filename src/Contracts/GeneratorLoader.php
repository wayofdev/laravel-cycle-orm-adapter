<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Contracts;

use Cycle\Schema\GeneratorInterface;

interface GeneratorLoader
{
    public const GROUP_INDEX = 'index';

    public const GROUP_RENDER = 'render';

    public const GROUP_POSTPROCESS = 'postprocess';

    /**
     * Add a generator to the loader.
     *
     * @param self::GROUP_* $group
     * @param class-string<GeneratorInterface>|GeneratorInterface $generator
     *
     * @return static
     */
    public function add(string $group, GeneratorInterface|string $generator): self;

    /**
     * Remove a generator from the loader.
     *
     * @param class-string<GeneratorInterface> $removableGenerator
     *
     * @return static
     */
    public function remove(string $removableGenerator): self;

    /**
     * Get the list of generators.
     *
     * @return array<GeneratorInterface>
     */
    public function get(): array;

    /**
     * Clear all generators from the loader.
     *
     * @return static
     */
    public function without(): self;
}
