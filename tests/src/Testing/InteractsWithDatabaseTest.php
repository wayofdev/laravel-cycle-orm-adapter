<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Testing;

use Cycle\ORM\EntityManagerInterface;
use Override;
use PHPUnit\Framework\Attributes\Test;
use WayOfDev\App\Entities\Post;
use WayOfDev\Tests\TestCase;

final class InteractsWithDatabaseTest extends TestCase
{
    /**
     * Set up the test environment.
     */
    #[Override]
    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('cycle:migrate:init');
        $this->artisan('cycle:orm:migrate', ['--force' => true]);
        $this->artisan('cycle:migrate', ['--force' => true]);
    }

    /**
     * Test that it asserts database has and missing records.
     */
    #[Test]
    public function it_asserts_database_has_and_missing(): void
    {
        $em = app(EntityManagerInterface::class);
        $post = new Post('Title', 'Description');
        $em->persist($post);
        $em->run();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id(),
            'title' => 'Title',
        ]);

        $this->assertDatabaseMissing('posts', [
            'id' => 999,
        ]);
    }

    /**
     * Test that it asserts the record count in the database.
     */
    #[Test]
    public function it_asserts_database_count(): void
    {
        $em = app(EntityManagerInterface::class);
        $post1 = new Post('Title 1', 'Description 1');
        $post2 = new Post('Title 2', 'Description 2');
        $em->persist($post1);
        $em->persist($post2);
        $em->run();

        $this->assertDatabaseCount('posts', 2);
    }

    /**
     * Test that it asserts the database is empty.
     */
    #[Test]
    public function it_asserts_database_empty(): void
    {
        $this->assertDatabaseEmpty('posts');
    }

    /**
     * Test that it asserts database has with iterable tables.
     */
    #[Test]
    public function it_asserts_database_has_with_iterable(): void
    {
        $em = app(EntityManagerInterface::class);
        $post = new Post('Title', 'Description');
        $em->persist($post);
        $em->run();

        $this->assertDatabaseHas(['posts'], [
            'id' => $post->id(),
            'title' => 'Title',
        ]);
    }

    /**
     * Test that it asserts database count with iterable tables.
     */
    #[Test]
    public function it_asserts_database_count_with_iterable(): void
    {
        $em = app(EntityManagerInterface::class);
        $post1 = new Post('Title 1', 'Description 1');
        $post2 = new Post('Title 2', 'Description 2');
        $em->persist($post1);
        $em->persist($post2);
        $em->run();

        $this->assertDatabaseCount(['posts'], 2);
    }

    /**
     * Test that it asserts database has with entity class as table.
     */
    #[Test]
    public function it_asserts_database_has_with_entity_class(): void
    {
        $em = app(EntityManagerInterface::class);
        $post = new Post('Title', 'Description');
        $em->persist($post);
        $em->run();

        $this->assertDatabaseHas(Post::class, [
            'id' => $post->id(),
            'title' => 'Title',
        ]);
    }

    /**
     * Test that it asserts database has with iterable of entities.
     */
    #[Test]
    public function it_asserts_database_has_with_iterable_of_entities(): void
    {
        $em = app(EntityManagerInterface::class);
        $post = new Post('Title', 'Description');
        $em->persist($post);
        $em->run();

        $this->assertDatabaseHas([$post], [
            'id' => $post->id(),
            'title' => 'Title',
        ]);
    }

    /**
     * Test that it asserts database is empty with iterable tables.
     */
    #[Test]
    public function it_asserts_database_empty_with_iterable(): void
    {
        $this->assertDatabaseEmpty(['posts']);
    }
}
