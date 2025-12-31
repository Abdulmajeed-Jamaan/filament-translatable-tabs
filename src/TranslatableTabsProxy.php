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
        $tabs = TranslatableTabs::make($this->repeater->getLabel())
             ->when(! is_null($this->locales), fn (TranslatableTabs $tabs) => $tabs->locales($this->locales))
             ->when(! is_null($this->modifyTabsUsing), fn (TranslatableTabs $tabs) => $tabs->modifyTabsUsing($this->modifyTabsUsing))
             ->when(! is_null($this->modifyFieldsUsing), fn (TranslatableTabs $tabs) => $tabs->modifyFieldsUsing($this->modifyFieldsUsing))
             ->schema($components);

        return $this->repeater->schema([$tabs]);
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
