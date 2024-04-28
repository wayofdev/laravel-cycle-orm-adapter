<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Testing;

use Cycle\ORM\ORMInterface;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use Throwable;
use WayOfDev\App\Entities\Post;
use WayOfDev\App\Repositories\PostRepository;
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
     * @throws Throwable
     */
    #[Test]
    public function it_soft_deletes_post_entity(): void
    {
        /** @var PostRepository $repository */
        $repository = $this->orm->getRepository(Post::class);
        $post = new Post('Title', 'Description');
        $deletedAt = new DateTimeImmutable();

        $repository->persist($post);

        $this->assertNotSoftDeleted('posts', [
            'id' => $post->id(),
        ]);

        $post->softDelete(new DateTimeImmutable());

        $repository->persist($post);

        $this->assertSoftDeleted('posts', [
            'id' => $post->id(),
        ]);

        $this::assertEquals($deletedAt->format('Y-m-d H:i:s'), $post->deletedAt()->format('Y-m-d H:i:s'));
    }
}
