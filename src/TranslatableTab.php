<?php

namespace AbdulmajeedJamaan\FilamentTranslatableTabs;

use Filament\Forms\Components\Tabs\Tab;

class TranslatableTab extends Tab
{
    protected string $locale;

    public function locale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
