<?php

declare(strict_types=1);

namespace WayOfDev\App\Entities;

use Cycle\Annotated\Annotation\Relation\Embedded;

trait HasSignatures
{
    #[Embedded(target: Signature::class, prefix: 'created_')]
    private Signature $created;

    #[Embedded(target: Signature::class, prefix: 'updated_')]
    private Signature $updated;

    #[Embedded(target: Signature::class, prefix: 'deleted_')]
    private ?Signature $deleted;

    public function created(): Signature
    {
        return $this->created;
    }

    public function updated(): Signature
    {
        return $this->updated;
    }

    public function deleted(): ?Signature
    {
        if (! $this->deleted?->defined()) {
            return null;
        }

        return $this->deleted;
    }

    public function softDelete(Signature $deleted): void
    {
        $this->deleted = $deleted;
    }
}
