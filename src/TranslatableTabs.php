<?php

namespace AbdulmajeedJamaan\FilamentTranslatableTabs;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Tabs;
use http\Exception\RuntimeException;

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

    public function handleModifyTabsUsing(TranslatableTab $tab): void
    {
        foreach ($this->modifyTabsUsing as $closure) {
            $tab->evaluate($closure, ['locale' => $tab->getLocale()]);
        }
    }

    public function handleModifyFieldsUsing(TranslatableTab $tab, Field $field): void
    {
        foreach ($this->modifyFieldsUsing as $closure) {
            $field->evaluate($closure, ['locale' => $tab->getLocale()]);
        }
    }

    /**
     * @return array<Component>
     */
    public function getChildComponents(): array
    {
        /**
         * @var array<Field> $components
         */
        $components = parent::getChildComponents();

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

    public function getChildComponentContainer($key = null): ComponentContainer
    {
        $componentContainer = parent::getChildComponentContainer($key);

        /**
         * @var TranslatableTab $tab
         */
        foreach ($componentContainer->getComponents() as $tab) {
            $this->handleModifyTabsUsing($tab);

            /**
             * @var Field $field
             */
            foreach ($tab->getChildComponentContainer()->getComponents() as $field) {
                $this->handleModifyFieldsUsing($tab, $field);
            }
        }

        return $componentContainer;
    }
}
