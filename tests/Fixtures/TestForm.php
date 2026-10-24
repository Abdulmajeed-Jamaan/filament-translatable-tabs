<?php

namespace AbdulmajeedJamaan\FilamentTranslatableTabs\Tests\Fixtures;

use Closure;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

class TestForm extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    /**
     * @var Closure(): array<\Filament\Schemas\Components\Component>
     */
    public static Closure $components;

    /**
     * @var array<string, mixed>
     */
    public static array $initialState = [];

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(static::$initialState);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components((static::$components)())
            ->statePath('data');
    }

    public function render(): string
    {
        return '<div>{{ $this->form }}</div>';
    }
}
