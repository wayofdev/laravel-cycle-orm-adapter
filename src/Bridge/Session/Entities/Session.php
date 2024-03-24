<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Session\Entities;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;

#[Index(columns: ['user_id'])]
#[Index(columns: ['last_activity'])]
#[Entity(table: 'sessions')]
class Session
{
    #[Column(type: 'string', primary: true)]
    public string $id;

    #[Column(type: 'integer', nullable: true)]
    // @todo Set foreign key to user table
    // https://github.com/laravel/laravel/blob/11.x/database/migrations/0001_01_01_000000_create_users_table.php#L32
    public ?int $userId;

    #[Column(type: 'string(45)', nullable: true)]
    public ?string $ipAddress;

    #[Column(type: 'text', nullable: true)]
    public ?string $userAgent;

    #[Column(type: 'longText')]
    public string $payload;

    #[Column(type: 'integer')]
    public int $lastActivity;
}
