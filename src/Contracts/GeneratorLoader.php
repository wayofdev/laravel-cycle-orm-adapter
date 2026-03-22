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
     * @param self::GROUP_* $group
     * @param class-string<GeneratorInterface>|GeneratorInterface $generator
     *
     * @return static
     */
    public function add(string $group, GeneratorInterface|string $generator): self;

    /**
     * @param class-string<GeneratorInterface> $removableGenerator
     *
     * @return static
     */
    public function remove(string $removableGenerator): self;

    /**
     * @return array<GeneratorInterface>
     */
    public function get(): array;

    /**
     * @return static
     */
    public function without(): self;
}
