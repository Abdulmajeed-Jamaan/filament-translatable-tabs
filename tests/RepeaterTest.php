<?php

use Livewire\Component;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use AbdulmajeedJamaan\FilamentTranslatableTabs\TranslatableTabs;

class TestLivewireComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;
    public function render() { return <<<'blade'
    <div></div>
    blade; }
}

it('can create a repeater with translatable tabs syntax', function () {
    $component = new TestLivewireComponent();
    $schema = Schema::make($component)
        ->components([
            Repeater::make('posts')
                ->translatableTabs(['en' => 'English', 'ar' => 'Arabic'])
                ->schema([
                    TextInput::make('title'),
                ]),
        ]);

    $repeater = $schema->getComponents()[0];

    // The returned component should be a Repeater (via Proxy verification, relying on getComponents result)
    expect($repeater)->toBeInstanceOf(Repeater::class);

    // The Repeater should have 1 child component (The TranslatableTabs)
    $children = $repeater->getChildComponents();
    expect($children)->toHaveCount(1);

    // That child should be an instance of TranslatableTabs
    $translatableTabs = $children[0];
    expect($translatableTabs)->toBeInstanceOf(TranslatableTabs::class);

    // The TranslatableTabs should have children (tabs/fields)
    expect($translatableTabs->getChildComponents())->not->toBeEmpty();
});
