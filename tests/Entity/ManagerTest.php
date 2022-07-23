<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Tests\Entity;

use ReflectionException;
use WayOfDev\Cycle\Entity\Manager;
use WayOfDev\Cycle\Tests\Stubs\UserFactory;
use WayOfDev\Cycle\Tests\TestCase;

final class ManagerTest extends TestCase
{
    /**
     * @test
     *
     * @throws ReflectionException
     */
    public function it_should_persist_entity_using_entity_manager(): void
    {
        /** @var User $user */
        $user = UserFactory::new()->make();

        $this->app->make(Manager::class)->persist($user);

        $this->assertDatabaseHas('users', [
            'id' => $user->getId(),
        ]);
    }

    /**
     * @test
     *
     * @throws ReflectionException
     */
    public function it_should_delete_entity_using_entity_manager(): void
    {
        $user = UserFactory::new()->make();

        $entityManager = $this->app->make(Manager::class);
        $entityManager->persist($user);
        $this->assertDatabaseCount('users', 1);

        $entityManager->delete($user);
        $this->assertDatabaseCount('users', 0);
    }
}
