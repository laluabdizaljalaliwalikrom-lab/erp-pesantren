<?php

declare(strict_types=1);

namespace App\Filament\Resources\ExpenseCategories\Schemas;

use App\Models\ExpenseCategory;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ExpenseCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Kategori')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', str($state)->slug())),

                TextInput::make('slug')
                    ->label('Slug (Otomatis)')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->unique(ExpenseCategory::class, 'slug', ignoreRecord: true),
            ])->columns(1);
    }
}
