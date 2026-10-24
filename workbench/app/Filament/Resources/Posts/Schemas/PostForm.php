<?php

namespace Workbench\App\Filament\Resources\Posts\Schemas;

use AbdulmajeedJamaan\FilamentTranslatableTabs\TranslatableTabs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),

                // Single field
                TextInput::make('title')
                    ->required()
                    ->translatableTabs()
                    ->columnSpanFull(),

                // Multiple fields
                TranslatableTabs::make('Body')
                    ->schema([
                        TextInput::make('summary'),
                        Textarea::make('content')
                            ->rows(5),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
