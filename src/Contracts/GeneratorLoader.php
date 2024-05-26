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
     * @return $this
     */
    public function add(string $group, GeneratorInterface|string $generator): self;

    /**
     * @param class-string<GeneratorInterface> $removableGenerator
     *
     * @return $this
     */
    public function remove(string $removableGenerator): self;

    /**
     * @return array<GeneratorInterface>
     */
    public function get(): array;

    public function without(): self;
}
