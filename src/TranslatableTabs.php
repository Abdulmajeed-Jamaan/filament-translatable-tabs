<?php

namespace AbdulmajeedJamaan\FilamentTranslatableTabs;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use RuntimeException;
use SplObjectStorage;

class TranslatableTabs extends Tabs
{
    use HasExtraConfigs;

    /**
     * @var array<string, string>|Closure(): array<string, string>
     */
    protected array | Closure $localeLabels;

    /**
     * @var array<string|int, string>|Closure(): array<string|int, string>
     */
    protected array | Closure $locales;

    /**
     * @var array<Closure>
     */
    protected array $modifyTabsUsing = [];

    /**
     * @var array<Closure>
     */
    protected array $modifyFieldsUsing = [];

    /**
     * @var SplObjectStorage<Component, true>|null
     */
    protected ?SplObjectStorage $modifiedComponents = null;

    /**
     * @param  array<string, string>|Closure(): array<string, string>  $localesLabels
     */
    public function localesLabels(array | Closure $localesLabels): static
    {
        $this->localeLabels = $localesLabels;

        return $this;
    }

    /**
     * @param  array<string|int, string>|Closure(): array<string|int, string>  $locales
     * @return $this
     */
    public function locales(array | Closure $locales): static
    {
        $this->locales = $locales;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getLocales(): array
    {
        $localeLabels = $this->evaluate($this->localeLabels);

        return collect($this->evaluate($this->locales))
            ->mapWithKeys(
                fn ($label, $locale) => is_int($locale)
                ? [$label => $localeLabels[$label]]
                : [$locale => $label]
            )
            ->toArray();
    }

    public function modifyTabsUsing(Closure $closure, bool $merge = true): static
    {
        if ($merge) {
            $this->modifyTabsUsing[] = $closure;
        } else {
            $this->modifyTabsUsing = [$closure];
        }

        return $this;
    }

    public function modifyFieldsUsing(Closure $closure, bool $merge = true): static
    {
        if ($merge) {
            $this->modifyFieldsUsing[] = $closure;
        } else {
            $this->modifyFieldsUsing = [$closure];
        }

        return $this;
    }

    protected function hasAlreadyModified(Component $component): bool
    {
        $this->modifiedComponents ??= new SplObjectStorage;

        if ($this->modifiedComponents->contains($component)) {
            return true;
        }

        $this->modifiedComponents->attach($component);

        return false;
    }

    public function handleModifyTabsUsing(TranslatableTab $tab): void
    {
        if ($this->hasAlreadyModified($tab)) {
            return;
        }

        foreach ($this->modifyTabsUsing as $closure) {
            $tab->evaluate($closure, ['locale' => $tab->getLocale()]);
        }
    }

    public function handleModifyFieldsUsing(TranslatableTab $tab, Field $field): void
    {
        if ($this->hasAlreadyModified($field)) {
            return;
        }

        foreach ($this->modifyFieldsUsing as $closure) {
            $field->evaluate($closure, ['locale' => $tab->getLocale()]);
        }
    }

    /**
     * @return array<Component>
     */
    public function getDefaultChildComponents(): array
    {
        /**
         * @var array $components
         */
        $components = parent::getDefaultChildComponents();

        if (filled($components) && collect($components)->every(fn ($component) => $component instanceof TranslatableTab)) {
            return $components;
        }

        if (collect($components)->contains(fn ($component) => ! $component instanceof Field)) {
            throw new RuntimeException('Only instances of type ' . Field::class . ' Supported');
        }

        $tabs = [];

        foreach ($this->getLocales() as $locale => $label) {
            $fields = [];

            foreach ($components as $component) {
                $field = $component
                    ->getClone()
                    ->name("{$component->getName()}.$locale")
                    ->label($component->getLabel())
                    ->statePath("{$component->getStatePath(false)}.$locale");

                $fields[] = $field;
            }

            $tab = TranslatableTab::make($label)
                ->locale($locale)
                ->schema($fields);

            $tabs[] = $tab;
        }

        return $tabs;
    }

    public function getChildSchema($key = null): Schema
    {
        $componentContainer = parent::getChildSchema($key);
        /**
         * @var TranslatableTab $tab
         */
        foreach ($componentContainer->getComponents() as $tab) {
            $this->handleModifyTabsUsing($tab);

            /**
             * @var Field $field
             */
            foreach ($tab->getChildSchema()->getComponents() as $field) {
                $this->handleModifyFieldsUsing($tab, $field);
            }
        }

        return $componentContainer;
    }
}
