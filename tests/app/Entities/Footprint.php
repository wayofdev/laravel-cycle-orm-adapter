<?php

declare(strict_types=1);

namespace WayOfDev\App\Entities;

use Cycle\Database\DatabaseInterface;
use JsonException;
use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Stringable;

use function json_decode;
use function json_encode;

final class Footprint implements JsonSerializable, Stringable
{
    private readonly UserId $id;

    private readonly string $party;

    private readonly string $realm;

    public static function empty(string $authorizedParty = 'guest-party', string $realm = 'guest-realm'): self
    {
        return new self(UserId::create(Uuid::NIL), $authorizedParty, $realm);
    }

    public static function random(string $authorizedParty = 'random-party', string $realm = 'random-realm'): self
    {
        return new self(UserId::create(Uuid::uuid7()->toString()), $authorizedParty, $realm);
    }

    public static function fromArray(array $data): self
    {
        $userId = UserId::fromString($data['id']);

        return new self($userId, $data['party'], $data['realm']);
    }

    /**
     * https://cycle-orm.dev/docs/advanced-column-wrappers/2.x/en.
     *
     * @throws JsonException
     */
    public static function castValue(string $value, DatabaseInterface $db): self
    {
        return self::fromArray(
            json_decode($value, true, 512, JSON_THROW_ON_ERROR)
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'party' => $this->party,
            'realm' => $this->realm,
        ];
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function party(): string
    {
        return $this->party;
    }

    public function realm(): string
    {
        return $this->realm;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @throws JsonException
     */
    public function __toString(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    private function __construct(UserId $id, string $party, string $realm)
    {
        $this->id = $id;
        $this->party = $party;
        $this->realm = $realm;
    }
}
