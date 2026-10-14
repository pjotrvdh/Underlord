<?php

declare(strict_types=1);

namespace App\Models\Contracts;

/**
 * A game data record whose defaults live in config/underlord/*.php.
 */
interface HasDefaults
{
    /** @return array<string, mixed>|null */
    public function configDefaults(): ?array;

    public function defaultValue(string $field): mixed;
}
