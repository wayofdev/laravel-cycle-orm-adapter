<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Rules;

use Cycle\Database\DatabaseInterface;
use Cycle\ORM\EntityManagerInterface;
use Illuminate\Translation\PotentiallyTranslatedString;
use Override;
use PHPUnit\Framework\Attributes\Test;
use WayOfDev\App\Entities\Post;
use WayOfDev\Cycle\Bridge\Laravel\Rules\Exists;
use WayOfDev\Cycle\Bridge\Laravel\Rules\Unique;
use WayOfDev\Tests\TestCase;

final class ValidationRulesTest extends TestCase
{
    private DatabaseInterface $database;

    /**
     * Set up the test environment.
     */
    #[Override]
    public function setUp(): void
    {
        parent::setUp();

        $this->database = app(DatabaseInterface::class);

        $this->artisan('cycle:migrate:init');
        $this->artisan('cycle:orm:migrate', ['--force' => true]);
        $this->artisan('cycle:migrate', ['--force' => true]);
    }

    /**
     * Test that it validates the exists rule.
     */
    #[Test]
    public function it_validates_exists_rule(): void
    {
        $em = app(EntityManagerInterface::class);
        $post = new Post('Title', 'Description');
        $em->persist($post);
        $em->run();

        $rule = new Exists($this->database, 'posts', 'id');

        $passed = true;
        $rule->validate('id', $post->id(), function (string $message) use (&$passed): PotentiallyTranslatedString {
            $passed = false;

            return new PotentiallyTranslatedString($message, app('translator'));
        });

        self::assertTrue($passed);

        $passed = true;
        $rule->validate('id', 999, function (string $message) use (&$passed): PotentiallyTranslatedString {
            $passed = false;

            return new PotentiallyTranslatedString($message, app('translator'));
        });

        self::assertFalse($passed);
    }

    /**
     * Test that it validates the unique rule.
     */
    #[Test]
    public function it_validates_unique_rule(): void
    {
        $em = app(EntityManagerInterface::class);
        $post = new Post('Title', 'Description');
        $em->persist($post);
        $em->run();

        $rule = new Unique($this->database, 'posts', 'id');

        $passed = true;
        $rule->validate('id', 999, function (string $message) use (&$passed): PotentiallyTranslatedString {
            $passed = false;

            return new PotentiallyTranslatedString($message, app('translator'));
        });

        self::assertTrue($passed);

        $passed = true;
        $rule->validate('id', $post->id(), function (string $message) use (&$passed): PotentiallyTranslatedString {
            $passed = false;

            return new PotentiallyTranslatedString($message, app('translator'));
        });

        self::assertFalse($passed);
    }
}
