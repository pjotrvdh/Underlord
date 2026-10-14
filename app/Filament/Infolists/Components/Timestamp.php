<?php

namespace App\Filament\Infolists\Components;

use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Override;

class Timestamp extends TextEntry
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('resource.fields.'.$this->getName()));
        $this->state(fn (Model $record): ?string => ($value = $record->{$this->getName()}) instanceof Carbon
                ? $value->translatedFormat('j F Y - H:i')
                : null
        );
        $this->hint(fn (Model $record): ?string => ($value = $record->{$this->getName()}) instanceof Carbon
                ? $value->diffForHumans()
                : null
        );
        $this->visible(fn ($state): bool => (bool) $state);
        $this->hidden(fn (?Model $record): bool => ! $record instanceof Model);
    }
}
