<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Bridge\Laravel\Rules;

use Closure;
use Cycle\Database\DatabaseInterface;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class Exists implements ValidationRule
{
    /**
     * Create a new rule instance.
     */
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
        $count = $this->database
            ->select()
            ->from($this->table)
            ->where([$this->column => $value])
            ->count();

        if ($count === 0) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     */
    public function message(): PotentiallyTranslatedString|string
    {
        return trans('validation.exists');
    }
}
