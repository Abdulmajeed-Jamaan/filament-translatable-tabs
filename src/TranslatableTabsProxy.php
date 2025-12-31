<?php

namespace AbdulmajeedJamaan\FilamentTranslatableTabs;

use Closure;
use Filament\Forms\Components\Repeater;

class TranslatableTabsProxy
{
    protected Repeater $repeater;
    protected array | Closure | null $locales = null;
    protected ?Closure $modifyTabsUsing = null;
    protected ?Closure $modifyFieldsUsing = null;

    public function __construct(
        Repeater $repeater,
        array | Closure | null $locales = null,
        ?Closure $modifyTabsUsing = null,
        ?Closure $modifyFieldsUsing = null
    ) {
        $this->repeater = $repeater;
        $this->locales = $locales;
        $this->modifyTabsUsing = $modifyTabsUsing;
        $this->modifyFieldsUsing = $modifyFieldsUsing;
    }

    public function schema(array | Closure $components): Repeater
    {
        $isTable = method_exists($this->repeater, 'getTableColumns') && ! empty($this->repeater->getTableColumns());

        if ($isTable) {
            if ($components instanceof Closure) {
                return $this->repeater->schema(function (...$args) use ($components) {
                    return $this->itemSchema($components(...$args));
                });
            }

            return $this->repeater->schema($this->itemSchema($components));
        }

        $tabs = $this->makeTranslatableTabs()
             ->schema($components);

        return $this->repeater->schema([$tabs]);
    }

    protected function makeTranslatableTabs(): TranslatableTabs
    {
        return TranslatableTabs::make($this->repeater->getLabel()) // We might want null label for table cells?
             ->when(! is_null($this->locales), fn (TranslatableTabs $tabs) => $tabs->locales($this->locales))
             ->when(! is_null($this->modifyTabsUsing), fn (TranslatableTabs $tabs) => $tabs->modifyTabsUsing($this->modifyTabsUsing))
             ->when(! is_null($this->modifyFieldsUsing), fn (TranslatableTabs $tabs) => $tabs->modifyFieldsUsing($this->modifyFieldsUsing));
    }

    protected function itemSchema(array $components): array
    {
        $schema = [];

        foreach ($components as $component) {
            $schema[] = $this->makeTranslatableTabs()
                ->label(null) // Hide label inside table cell
                ->schema([$component]);
        }

        return $schema;
    }

    public function __call(string $method, array $arguments): mixed
    {
        $result = $this->repeater->$method(...$arguments);

        if ($result === $this->repeater) {
            return $this;
        }

        return $result;
    }
}
