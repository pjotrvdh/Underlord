<?php

namespace App\Filament\Schemas;

use App\Filament\Infolists\Components\Timestamp;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Model;

/**
 * The admin form layout: the fields in a wide main section,
 * and a narrow aside with the record's fixed facts and when (and by whom) it was last changed.
 */
class FormLayout
{
    /**
     * @param  array<Component>  $main
     * @param  array<Component>  $aside
     */
    public static function make(array $main, array $aside = [], int $mainColumns = 2): Grid
    {
        return Grid::make([
            'default' => 1,
            'sm' => 1,
            'md' => 1,
            'lg' => 4,
            'xl' => 4,
            '2xl' => 4,
        ])
            ->schema([
                Section::make()
                    ->schema($main)
                    ->columns($mainColumns)
                    ->columnSpan(['lg' => 3]),
                Section::make()
                    ->schema([
                        ...$aside,
                        Group::make()
                            ->schema([
                                Timestamp::make('created_at'),
                                Timestamp::make('updated_at'),
                                TextEntry::make('updatedBy.name')
                                    ->label(__('resource.fields.updated_by'))
                                    ->visible(fn (?Model $record): bool => $record instanceof Model && method_exists($record, 'updatedBy') && $record->getAttribute('updated_by') !== null),
                            ])
                            ->hidden(fn (?Model $record): bool => ! $record instanceof Model),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columnSpanFull();
    }
}
