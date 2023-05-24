<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Factories;

use ReflectionException;
use WayOfDev\App\Entities\User;
use WayOfDev\App\Entities\UserFactory;
use WayOfDev\Tests\TestCase;

final class FactoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->artisanCall('cycle:migrate:init');
        $this->artisanCall('cycle:orm:migrate', ['--force' => true]);
        $this->artisanCall('cycle:migrate', ['--force' => true]);
    }

    /**
     * @test
     *
     * @throws ReflectionException
     */
    public function it_should_make_factory_without_persisting_using_helper(): void
    {
        /** @var User $user */
        $user = UserFactory::new()->make();

        $this->assertDatabaseMissing('users', [
            'id' => $user->getId(),
        ]);
    }

    /**
     * @test
     *
     * @throws ReflectionException
     */
    public function it_should_make_factory_without_persisting_statically(): void
    {
        /** @var User $user */
        $user = UserFactory::new()->make();

        $this->assertDatabaseMissing('users', [
            'id' => $user->getId(),
        ]);
    }

    /**
     * @test
     *
     * @throws ReflectionException
     */
    public function it_should_make_factory_and_persist_user_statically(): void
    {
        /** @var User $user */
        $user = UserFactory::new()->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->getId(),
        ]);
    }

    /**
     * @test
     */
    public function it_should_make_factory_and_persist_user_using_helper(): void
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->getId(),
        ]);
    }

    /**
     * @test
     */
    public function it_should_make_multiple_factories(): void
    {
        $users = factory(User::class, 15)->make();

        self::assertCount(15, $users);
    }
}
