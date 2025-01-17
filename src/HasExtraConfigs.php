<?php

namespace AbdulmajeedJamaan\FilamentTranslatableTabs;

use Filament\Forms\Components\Field;

trait HasExtraConfigs
{
    public function addDirectionByLocale(): static
    {
        $this->modifyFieldsUsing(function (Field $component, string $locale) {
            $dir = str($locale)->startsWith('ar') ? 'rtl' : 'ltr';
            $component->extraAttributes(['style' => "direction: $dir;"], true);
        });

        return $this;
    }

    public function addEmptyBadgeWhenAllFieldsAreEmpty(string $emptyLable): static
    {
        $this->modifyTabsUsing(function (TranslatableTab $component, string $locale) use ($emptyLable) {
            $hasValue = fn ($tab, $get): bool => collect($tab->getChildComponents())
                ->contains(fn ($c) => ! empty($get($c->getName())));

            $component
                ->live(true)
                ->badgeColor(fn ($component, $get) => $hasValue($component, $get) ? null : 'warning')
                ->badge(fn ($component, $get) => $hasValue($component, $get) ? null : $emptyLable);
        });

        return $this;
    }

    public function addSetActiveTabThatHasValue(): static
    {
        $this->activeTab(function ($get, $component) {
            $hasValue = function ($tab, $get): bool {
                foreach ($tab->getChildComponents() as $component) {
                    if (! empty($get($component->getName()))) {
                        return true;
                    }
                }

                return false;
            };

            $activeTabsIndex = collect($component->getChildComponents())
                ->search(fn ($tab) => $hasValue($tab, $get));

            return $activeTabsIndex === false ? 1 : $activeTabsIndex + 1;
        });

        return $this;
    }
}
