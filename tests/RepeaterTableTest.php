<?php

use Livewire\Component;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater\TableColumn; // Correct namespace? User used Repeater\TableColumn? Wait, UserForm used "Filament\Forms\Components\Repeater\TableColumn"
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use AbdulmajeedJamaan\FilamentTranslatableTabs\TranslatableTabs;

class TestLivewireComponentForTable extends Component implements HasSchemas
{
    use InteractsWithSchemas;
    public function render() { return '<div></div>'; }
}

it('can create a repeater with table mode and translatable tabs', function () {
    $component = new TestLivewireComponentForTable();

    // User usage: ->table([...])->schema([...])
    $schema = Schema::make($component)
        ->components([
            Repeater::make('posts')
                ->translatableTabs(['en' => 'English', 'ar' => 'Arabic'])
                ->table([
                     TableColumn::make('title'),
                     TableColumn::make('content'),
                ])
                ->schema([
                    TextInput::make('title'),
                    TextInput::make('content'),
                ]),
        ]);

    $repeater = $schema->getComponents()[0];

    $children = $repeater->getChildComponents();
    // Use expect($children)->toHaveCount(2); will FAIL.
    expect($children)->toHaveCount(2);

    // Also verify they are TranslatableTabs
    expect($children[0])->toBeInstanceOf(TranslatableTabs::class);
    expect($children[1])->toBeInstanceOf(TranslatableTabs::class);
});
