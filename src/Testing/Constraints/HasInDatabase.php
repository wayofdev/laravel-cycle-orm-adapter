<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Testing\Constraints;

use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseProviderInterface;
use JsonException;
use PHPUnit\Framework\Constraint\Constraint;
use Throwable;

use function array_key_first;
use function array_keys;
use function json_encode;
use function sprintf;

class HasInDatabase extends Constraint
{
    protected int $show = 3;

    protected DatabaseInterface $database;

    protected array $data;

    public function __construct(DatabaseProviderInterface $database, array $data)
    {
        $this->data = $data;

        $this->database = $database->database();
    }

    /**
     * @param string $table
     */
    public function matches($table): bool
    {
        $tableInterface = $this->database->table($table);

        try {
            $count = $tableInterface->where($this->data)->count();

            return 0 < $count;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * @throws JsonException
     */
    public function failureDescription(mixed $table): string
    {
        return sprintf(
            "a row in the table [%s] matches the attributes %s.\n\n%s",
            $table,
            $this->toString(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            $this->getAdditionalInfo($table)
        );
    }

    /**
     * @throws JsonException
     */
    public function toString(int $options = 0): string
    {
        return json_encode($this->data, JSON_THROW_ON_ERROR | $options);
    }

    protected function getAdditionalInfo(string $table): string
    {
        $query = $this->database->table($table);

        $similarResults = $query->where(
            array_key_first($this->data),
            $this->data[array_key_first($this->data)]
        )->select(array_keys($this->data))->limit($this->show)->get();

        if ($similarResults->isNotEmpty()) {
            $description = 'Found similar results: ' . json_encode($similarResults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            $query = $this->database->table($table);

            $results = $query->select(array_keys($this->data))->limit($this->show)->get();

            if ($results->isEmpty()) {
                return 'The table is empty';
            }

            $description = 'Found: ' . json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        if ($query->count() > $this->show) {
            $description .= sprintf(' and %s others', $query->count() - $this->show);
        }

        return $description;
    }
}
