# 🧪 Testing Guidelines

## 📋 Overview

This document provides comprehensive testing guidelines for the Laravel Cycle ORM Adapter project. These guidelines focus on testing the Laravel bridge functionality, ensuring Cycle ORM integrates seamlessly with Laravel's ecosystem while maintaining test reliability and performance.

## 🎯 Testing Philosophy

### 🔗 **Bridge-Focused Testing**

- **Primary Goal**: Test Laravel-Cycle ORM integration, not core Cycle ORM functionality
- **Integration-Heavy**: Use real Laravel service bindings and container resolution
- **Multi-Database**: Ensure compatibility across SQLite, MySQL, PostgreSQL, and SQL Server
- **Real Schema Testing**: Create actual database schemas rather than extensive mocking

### 🏗️ **Test Structure Approach**

- **Technical Layer Organization**: Mirror the `src/` directory structure in tests
- **Balanced Testing**: Combine integration tests with targeted unit tests where appropriate
- **Docker-First**: Primary development workflow uses Docker containers
- **Performance Balance**: Optimize for both test accuracy and reasonable execution speed

## 📁 Test Organization

### 🗂️ **Directory Structure**

```bash
tests/
├── app/                         # Test application (Laravel app structure)
│   ├── Entities/                # Cycle ORM entities for testing
│   ├── Repositories/            # Repository implementations and interfaces
│   └── database/                # Migrations and seeders
├── src/                         # Mirror of main src/ structure
│   ├── Bridge/                  # Laravel bridge component tests
│   │   ├── Cache/               # Cache integration tests
│   │   ├── Laravel/             # Core Laravel integration tests
│   │   ├── Queue/               # Queue integration tests
│   │   ├── Session/             # Session integration tests
│   │   └── Telescope/           # Telescope integration tests
│   ├── TestCase.php             # Base test case
│   └── Pest.php                 # Pest configuration
└── database/                    # Shared database files
```

### 🎯 **Test Types**

#### **1. Bridge Integration Tests** (`tests/src/Bridge/`)

Test Laravel service integrations and service provider functionality:

```php
#[Test]
public function it_registers_orm_as_singleton(): void
{
    $class1 = $this->app->get(ORMInterface::class);
    $class2 = $this->app->get(ORMInterface::class);

    $this::assertInstanceOf(ORMInterface::class, $class1);
    $this::assertSame($class1, $class2);
}
```

#### **2. Console Command Tests** (`tests/src/Bridge/Laravel/Console/`)

Test Artisan command functionality with output verification:

```php
#[Test]
public function it_runs_database_list_command(): void
{
    $this->artisanCall('cycle:db:list');
    $output = Artisan::output();

    $this::assertStringContainsString('tables found', $output);
}
```

#### **3. Entity/Repository Tests** (`tests/src/Testing/`)

Test Cycle ORM functionality within Laravel context:

```php
#[Test]
public function it_persists_and_retrieves_entity(): void
{
    /** @var PostRepository $repository */
    $repository = $this->orm->getRepository(Post::class);
    $post = new Post('Test Title', 'Test Description');

    $repository->persist($post);

    $this->assertDatabaseHas('posts', ['title' => 'Test Title']);
}
```

## 🏃 Running Tests

### 🐳 **Docker Commands (Recommended)**

#### **Run All Tests (SQLite)**

```bash
docker compose run --rm --no-deps app vendor/bin/pest --color=always
```

#### **Run Specific Test File**

```bash
docker compose run --rm --no-deps app vendor/bin/pest tests/src/Bridge/Laravel/Providers/CycleServiceProviderTest.php --color=always
```

#### **Run Specific Test Method**

```bash
docker compose run --rm --no-deps app vendor/bin/pest --filter="it_registers_orm_as_singleton" --color=always
```

#### **Run Tests with Coverage**

```bash
docker compose run --rm --no-deps app vendor/bin/pest --coverage --color=always
```

### 🗄️ **Database-Specific Testing**

#### **SQLite (Default - Fast Local Development)**

```bash
# Using composer script
docker compose run --rm --no-deps app composer test

# Direct pest command
docker compose run --rm --no-deps app vendor/bin/pest --color=always
```

#### **MySQL Testing**

```bash
# Start MySQL service
docker compose up -d mysql

# Run tests
docker compose run --rm --no-deps app composer test:mysql

# Alternative direct command
DB_CONNECTION=mysql DB_HOST=mysql docker compose run --rm --no-deps app vendor/bin/pest --color=always
```

#### **PostgreSQL Testing**

```bash
# Start PostgreSQL service
docker compose up -d pgsql

# Run tests
docker compose run --rm --no-deps app composer test:pgsql

# Alternative direct command
DB_CONNECTION=pgsql DB_HOST=pgsql docker compose run --rm --no-deps app vendor/bin/pest --color=always
```

#### **SQL Server Testing**

```bash
# Start SQL Server service
docker compose up -d sqlserver

# Run tests
docker compose run --rm --no-deps app composer test:sqlserver

# Alternative direct command
DB_CONNECTION=sqlserver DB_HOST=sqlserver docker compose run --rm --no-deps app vendor/bin/pest --color=always
```

### 💻 **Native PHP Commands (Alternative)**

#### **Prerequisites**

- PHP 8.2+ with required extensions
- Database drivers (pdo_mysql, pdo_pgsql, pdo_sqlsrv for respective databases)
- Local database instances running

#### **Commands**

```bash
# SQLite (default)
vendor/bin/pest --color=always

# With specific database
DB_CONNECTION=mysql DB_HOST=localhost vendor/bin/pest --color=always
```

## 🏗️ Test Implementation Patterns

### 🧱 **Base Test Case Setup**

All tests extend the custom `TestCase` class which provides:

```php
use WayOfDev\Tests\TestCase;

class MyTest extends TestCase
{
    // Automatic database refresh and cleanup
    // Access to faker() helper
    // Artisan command testing helpers
    // Custom database assertions
}
```

### 🏭 **Entity Creation Pattern**

Create entities using constructors with required parameters:

```php
#[Test]
public function it_creates_post_entity(): void
{
    $post = new Post('Post Title', 'Post Description');

    $this::assertEquals('Post Title', $post->title());
    $this::assertEquals('Post Description', $post->description());
}
```

### 🗄️ **Repository Testing Pattern**

Test repositories through ORM interface resolution:

```php
#[Test]
public function it_persists_entity_through_repository(): void
{
    /** @var PostRepository $repository */
    $repository = $this->orm->getRepository(Post::class);
    $post = new Post('Title', 'Description');

    $repository->persist($post);

    $this->assertDatabaseHas('posts', [
        'title' => 'Title',
        'description' => 'Description'
    ]);
}
```

### ⚙️ **Service Provider Testing Pattern**

Test Laravel service bindings and registrations:

```php
#[Test]
public function it_registers_service_interface(): void
{
    $service = $this->app->make(ServiceInterface::class);

    $this::assertInstanceOf(ServiceInterface::class, $service);
    $this::assertInstanceOf(ConcreteService::class, $service);
}
```

### 🖥️ **Console Command Testing Pattern**

Test Artisan commands with output assertions:

```php
#[Test]
public function it_displays_expected_output(): void
{
    $this->assertConsoleCommandOutputContainsStrings(
        'cycle:migrate',
        ['--force' => true],
        ['Migration completed', 'Tables created']
    );
}
```

## 🗄️ Database Testing Strategies

### 🔄 **Database Refresh Mechanism**

The `RefreshDatabase` trait automatically:

- Drops all foreign keys
- Drops all tables
- Ensures clean state for each test

```php
protected function refreshDatabase(): void
{
    $database = app(DatabaseProviderInterface::class)->database('default');

    // Drop foreign keys first
    foreach ($database->getTables() as $table) {
        $schema = $table->getSchema();
        foreach ($schema->getForeignKeys() as $foreign) {
            $schema->dropForeignKey($foreign->getColumns());
        }
        $schema->save(HandlerInterface::DROP_FOREIGN_KEYS);
    }

    // Drop tables
    foreach ($database->getTables() as $table) {
        $table->getSchema()->declareDropped()->save();
    }
}
```

### 🏗️ **Schema Setup Pattern**

For tests requiring database schemas:

```php
public function setUp(): void
{
    parent::setUp();

    $this->orm = app(ORMInterface::class);

    // Initialize migrations
    $this->artisan('cycle:migrate:init');
    $this->artisan('cycle:orm:migrate', ['--force' => true]);
    $this->artisan('cycle:migrate', ['--force' => true]);
}
```

### ✅ **Database Assertions**

Use custom database assertion methods:

```php
// Assert record exists
$this->assertDatabaseHas('posts', ['title' => 'Test Post']);

// Assert record doesn't exist
$this->assertDatabaseMissing('posts', ['title' => 'Deleted Post']);

// Assert record count
$this->assertDatabaseCount('posts', 3);

// Assert table is empty
$this->assertDatabaseEmpty('posts');

// Assert soft deletion
$this->assertSoftDeleted('posts', ['id' => $post->id()]);
$this->assertNotSoftDeleted('posts', ['id' => $post->id()]);
```

## 🔧 Laravel Bridge Integration Testing

### 📦 **Service Provider Testing**

#### **Registration Testing**

```php
#[Test]
public function it_registers_required_services(): void
{
    // Test interface bindings
    $this::assertTrue($this->app->bound(ORMInterface::class));
    $this::assertTrue($this->app->bound(EntityManagerInterface::class));

    // Test singleton registration
    $orm1 = $this->app->make(ORMInterface::class);
    $orm2 = $this->app->make(ORMInterface::class);
    $this::assertSame($orm1, $orm2);
}
```

#### **Configuration Testing**

```php
#[Test]
public function it_loads_configuration_correctly(): void
{
    $config = config('cycle');

    $this::assertIsArray($config);
    $this::assertArrayHasKey('databases', $config);
    $this::assertArrayHasKey('orm', $config);
}
```

### 🔌 **Laravel Service Integration**

#### **Cache Integration**

```php
#[Test]
public function it_integrates_with_laravel_cache(): void
{
    // Test cache service provider registration
    $this::assertFalse($this->app->providerIsLoaded(CacheServiceProvider::class));

    // Test with cache enabled
    $this->app->register(CacheServiceProvider::class);
    $this::assertTrue($this->app->providerIsLoaded(CacheServiceProvider::class));
}
```

#### **Queue Integration**

```php
#[Test]
public function it_integrates_with_laravel_queue(): void
{
    $this->app->register(QueueServiceProvider::class);

    // Test queue service integration
    $this::assertTrue($this->app->providerIsLoaded(QueueServiceProvider::class));
}
```

#### **Telescope Integration**

```php
#[Test]
public function it_registers_telescope_entities_when_enabled(): void
{
    $this->app->register(TelescopeServiceProvider::class);

    $this->assertConsoleCommandOutputContainsStrings(
        'cycle:orm:render',
        ['--no-color' => true],
        [
            '[telescopeEntry] :: default.telescope_entries',
            'Entity: WayOfDev\Cycle\Bridge\Telescope\Entities\TelescopeEntry'
        ]
    );
}
```

### 🖥️ **Console Command Integration**

#### **Database Commands**

```php
#[Test]
public function it_lists_database_tables(): void
{
    // Create test tables
    $database = $this->app->make(DatabaseInterface::class);
    $schema = $database->table('test_table')->getSchema();
    $schema->primary('id');
    $schema->save();

    $this->assertConsoleCommandOutputContainsStrings(
        'cycle:db:list',
        [],
        ['test_table']
    );
}
```

#### **Migration Commands**

```php
#[Test]
public function it_runs_migration_commands(): void
{
    $this->assertConsoleCommandOutputContainsStrings(
        'cycle:migrate:init',
        [],
        ['initialized']
    );
}
```

#### **ORM Commands**

```php
#[Test]
public function it_renders_orm_schema(): void
{
    $this->assertConsoleCommandOutputContainsStrings(
        'cycle:orm:render',
        ['--no-color' => true],
        ['Schema:', 'Entity:']
    );
}
```

## 🔍 Testing Best Practices

### ✅ **DO This**

#### **Entity Testing**

```php
// ✅ Create real entities with constructors
$user = new User('John Doe');
$user->email = 'john@example.com';

// ✅ Test entity behavior, not just data
$user->addFriend($friendUser);
$this::assertCount(1, $user->friendsAsArray);
```

#### **Repository Testing**

```php
// ✅ Test through ORM interface
$repository = $this->orm->getRepository(User::class);
$user = new User('Test User');
$repository->persist($user);

// ✅ Use custom repository methods
$foundUser = $repository->findByUsername('testuser');
```

#### **Integration Testing**

```php
// ✅ Test real Laravel service integration
$service = $this->app->make(ServiceInterface::class);
$this::assertInstanceOf(ConcreteImplementation::class, $service);

// ✅ Test configuration loading
$config = config('cycle.orm');
$this::assertIsArray($config);
```

#### **Database Testing**

```php
// ✅ Use custom database assertions
$this->assertDatabaseHas('users', ['name' => 'John Doe']);
$this->assertDatabaseCount('users', 1);

// ✅ Test relationships
$this->assertDatabaseHas('user_roles', [
    'user_id' => $user->getId(),
    'role_id' => $role->getId()
]);
```

### ❌ **DON'T Do This**

#### **Avoid Over-Mocking**

```php
// ❌ Don't mock Cycle ORM entities
$user = $this->createMock(User::class);
$user->method('getName')->willReturn('John');

// ✅ Create real entities instead
$user = new User('John');
```

#### **Avoid Testing Core Cycle ORM**

```php
// ❌ Don't test core ORM functionality
$this::assertTrue($orm->hasEntity(User::class)); // ORM's responsibility

// ✅ Test Laravel integration instead
$this::assertTrue($this->app->bound(ORMInterface::class)); // Bridge's responsibility
```

#### **Avoid Complex Mocking**

```php
// ❌ Don't mock container bindings unnecessarily
$this->app->instance(ORMInterface::class, $mockOrm);

// ✅ Use real container resolution
$orm = $this->app->make(ORMInterface::class);
```

## 🚨 Troubleshooting & Debugging

### 🐳 **Docker Issues**

#### **Container Permission Issues**

```bash
# Fix file permissions
docker compose run --rm --no-deps app chown -R 1000:1000 /app/tests
```

#### **Database Connection Issues**

```bash
# Ensure database services are running
docker compose up -d mysql pgsql sqlserver

# Check service health
docker compose ps

# View service logs
docker compose logs mysql
```

#### **Memory Issues**

```bash
# Increase memory limit
docker compose run --rm --no-deps app php -d memory_limit=2G vendor/bin/pest
```

### 🗄️ **Database Issues**

#### **Schema Conflicts**

```php
// Clean up migrations in tearDown if needed
protected function tearDown(): void
{
    $this->cleanupMigrations($this->migrationsPath . '/*.php');
    parent::tearDown();
}
```

#### **Connection Timeouts**

- Ensure database services have sufficient startup time
- Check health checks in docker-compose.yml
- Verify connection credentials in test environment

#### **SQL Server Specific Issues**

```bash
# Ensure ODBC drivers are installed (for native PHP)
# Check connection string format in config
DB_CONNECTION=sqlserver DB_HOST=sqlserver DB_PORT=1433
```

### 🔧 **Test Execution Issues**

#### **Pest Configuration Issues**

```bash
# Clear Pest cache
rm -rf tests/.pest

# Run with verbose output
vendor/bin/pest --verbose
```

#### **Laravel Application Issues**

```bash
# Clear application cache
docker compose run --rm --no-deps app php artisan config:clear
docker compose run --rm --no-deps app php artisan cache:clear
```

### 📊 **Performance Issues**

#### **Slow Test Execution**

```bash
# Run specific test groups
vendor/bin/pest --group=fast

# Use SQLite for faster local testing
DB_CONNECTION=sqlite vendor/bin/pest

# Run tests in parallel (if using paratest)
vendor/bin/paratest --processes=4
```

## 📚 Reference Examples

### 🏗️ **Complete Test Example**

```php
<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Integration;

use Cycle\ORM\ORMInterface;
use PHPUnit\Framework\Attributes\Test;
use WayOfDev\App\Entities\User;
use WayOfDev\App\Repositories\UserRepository;
use WayOfDev\Tests\TestCase;

class UserRepositoryIntegrationTest extends TestCase
{
    private ORMInterface $orm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orm = app(ORMInterface::class);

        // Setup database schema
        $this->artisan('cycle:migrate:init');
        $this->artisan('cycle:orm:migrate', ['--force' => true]);
        $this->artisan('cycle:migrate', ['--force' => true]);
    }

    #[Test]
    public function it_persists_and_retrieves_user_entity(): void
    {
        /** @var UserRepository $repository */
        $repository = $this->orm->getRepository(User::class);

        // Create test entity
        $user = new User('John Doe');
        $user->email = 'john.doe@example.com';

        // Persist entity
        $repository->persist($user);

        // Assert database state
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com'
        ]);

        // Test repository method
        $foundUser = $repository->findByUsername('john.doe@example.com');
        $this::assertNotNull($foundUser);
        $this::assertEquals('John Doe', $foundUser->getName());
    }

    #[Test]
    public function it_handles_user_relationships(): void
    {
        $user = new User('Main User');
        $friend = new User('Friend User');

        // Test relationship
        $user->addFriend($friend);

        $repository = $this->orm->getRepository(User::class);
        $repository->persist($user);
        $repository->persist($friend);

        // Verify relationship in database
        $this->assertDatabaseHas('users', ['name' => 'Main User']);
        $this->assertDatabaseHas('users', ['name' => 'Friend User']);

        // Test relationship behavior
        $this::assertCount(1, $user->friendsAsArray);
        $this::assertSame($user, $friend->friend);
    }
}
```

### 🔧 **Service Provider Test Example**

```php
<?php

declare(strict_types=1);

namespace WayOfDev\Tests\Bridge\Laravel\Providers;

use Cycle\ORM\ORMInterface;
use PHPUnit\Framework\Attributes\Test;
use WayOfDev\Cycle\Bridge\Laravel\Providers\CycleServiceProvider;
use WayOfDev\Tests\TestCase;

class CycleServiceProviderTest extends TestCase
{
    #[Test]
    public function it_registers_required_bindings(): void
    {
        // Test provider is loaded
        $this::assertTrue($this->app->providerIsLoaded(CycleServiceProvider::class));

        // Test interface binding
        $this::assertTrue($this->app->bound(ORMInterface::class));

        // Test singleton behavior
        $orm1 = $this->app->make(ORMInterface::class);
        $orm2 = $this->app->make(ORMInterface::class);
        $this::assertSame($orm1, $orm2);

        // Test configuration
        $config = config('cycle');
        $this::assertIsArray($config);
        $this::assertArrayHasKey('databases', $config);
    }
}
```

---

## 📞 Need Help?

- 📖 Check [Contributing Guidelines](../docs/pages/contributing.mdx)
- 🐛 [Report Issues](https://github.com/wayofdev/laravel-cycle-orm-adapter/issues)
- 💬 [Discord Community](https://discord.gg/CE3TcCC5vr)
- 📧 [Email Support](mailto:the@wayof.dev)

---

*These guidelines are based on the current testing patterns used in the Laravel Cycle ORM Adapter project and are optimized for both human developers and AI-assisted development.* ✨
