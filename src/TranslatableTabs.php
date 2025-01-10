<?php

namespace AbdulmajeedJamaan\FilamentTranslatableTabs;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Tabs;

class TranslatableTabs extends Tabs
{
    /**
     * @var array<string, string>|Closure(): array<string, string>|null
     */
    protected static array | Closure | null $configureLocalesLabelsUsing = null;

    /**
     * @var array<string|int, string>|Closure(): array<string|int, string>|null
     */
    protected static array | Closure | null $configureLocalesUsing = null;

    /**
     * @var array<string|int, string>|Closure(): array<string|int, string>|null
     */
    protected array | Closure | null $locales = null;

    protected static ?Closure $configureTabsUsing = null;

    protected ?Closure $modifyTabsUsing = null;

    protected static ?Closure $configureFieldsUsing = null;

    protected ?Closure $modifyFieldsUsing = null;

    /**
     * @param  array<string, string>|Closure(): array<string, string>  $localesLabels
     */
    public static function configureLocalesLabelsUsing(array | Closure $localesLabels): void
    {
        static::$configureLocalesLabelsUsing = $localesLabels;
    }

    /**
     * @param  array<string|int, string>|Closure(): array<string|int, string>  $locales
     */
    public static function configureLocalesUsing(array | Closure $locales): void
    {
        static::$configureLocalesUsing = $locales;
    }

    /**
     * @param  array<string|int, string>|Closure(): array<string|int, string>  $locales
     * @return $this
     */
    public function locales(array | Closure | null $locales): static
    {
        $this->locales = $locales;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getLocales(): array
    {
        $localeLabels = $this->evaluate(static::$configureLocalesLabelsUsing);

        return collect($this->evaluate($this->locales) ?: $this->evaluate(static::$configureLocalesUsing))
            ->mapWithKeys(
                fn ($label, $locale) => is_int($locale)
                ? [$label => $localeLabels[$label]]
                : [$locale => $label]
            )
            ->toArray();
    }

    public static function configureTabsUsing(?Closure $closure): void
    {
        static::$configureTabsUsing = $closure;
    }

    public function modifyTabsUsing(?Closure $closure): static
    {
        $this->modifyTabsUsing = $closure;

        return $this;
    }

    public function handleModifyTabsUsing(Tabs\Tab $tab, $locale): void
    {
        if (static::$configureTabsUsing) {
            $tab->evaluate(static::$configureTabsUsing, ['locale' => $locale]);
        }

        if ($this->modifyTabsUsing) {
            $tab->evaluate($this->modifyTabsUsing, ['locale' => $locale]);
        }
    }

    public static function configureFieldsUsing(?Closure $closure): void
    {
        static::$configureFieldsUsing = $closure;
    }

    public function modifyFieldsUsing(?Closure $closure): static
    {
        $this->modifyFieldsUsing = $closure;

        return $this;
    }

    public function handleModifyFieldsUsing(Field $field, string $locale): void
    {
        if (static::$configureFieldsUsing) {
            $field->evaluate(static::$configureFieldsUsing, ['locale' => $locale]);
        }

        if ($this->modifyFieldsUsing) {
            $field->evaluate($this->modifyFieldsUsing, ['locale' => $locale]);
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

        $tabs = [];

        foreach ($this->getLocales() as $locale => $label) {
            $fields = [];

            foreach ($components as $component) {
                $field = $component
                    ->getClone()
                    ->name("{$component->getName()}.$locale")
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
            $this->handleModifyTabsUsing($tab, $tab->getLocale());

            /**
             * @var Field $field
             */
            foreach ($tab->getChildComponentContainer()->getComponents() as $field) {
                $this->handleModifyFieldsUsing($field, $tab->getLocale());
            }
        }

        return $componentContainer;
    }
}
