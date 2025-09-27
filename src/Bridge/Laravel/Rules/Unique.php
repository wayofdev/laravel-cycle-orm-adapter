<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Rules;

use Closure;
use Cycle\Database\DatabaseInterface;
use Cycle\Database\Query\SelectQuery;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class Unique implements ValidationRule
{
    public function __construct(
        private DatabaseInterface $database,
        private string $table,
        private string $column = 'id',
    ) {
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var SelectQuery $table */
        $table = $this->database->table($this->table);

        $count = $table->where([$this->column => $value])->count();

        if ($count > 0) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     */
    public function message(): PotentiallyTranslatedString|string
    {
        return trans('validation.unique');
    }
}
