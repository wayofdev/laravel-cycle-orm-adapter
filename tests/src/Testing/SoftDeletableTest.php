<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Testing;

use Cycle\ORM\ORMInterface;
use DateTimeImmutable;
use ReflectionException;
use WayOfDev\App\Entities\Post;
use WayOfDev\Tests\TestCase;

final class SoftDeletableTest extends TestCase
{
    public ORMInterface $orm;

    public function setUp(): void
    {
        parent::setUp();

        $this->orm = app(ORMInterface::class);

        $this->artisan('cycle:migrate:init');
        $this->artisan('cycle:orm:migrate', ['--force' => true]);
        $this->artisan('cycle:migrate', ['--force' => true]);
    }

    /**
     * @test
     *
     * @throws ReflectionException
     */
    public function it_soft_deletes_post_entity(): void
    {
        $repository = $this->orm->getRepository(Post::class);
        $post = new Post('Title', 'Description');
        $deletedAt = new DateTimeImmutable();

        // @phpstan-ignore-next-line
        $repository->persist($post);

        $this->assertNotSoftDeleted('posts', [
            'id' => $post->id(),
        ]);

        $post->softDelete(new DateTimeImmutable());

        // @phpstan-ignore-next-line
        $repository->persist($post);

        $this->assertSoftDeleted('posts', [
            'id' => $post->id(),
        ]);

        $this::assertEquals($deletedAt->format('Y-m-d H:i:s'), $post->deletedAt()->format('Y-m-d H:i:s'));
    }
}
