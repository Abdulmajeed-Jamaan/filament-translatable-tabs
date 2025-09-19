<?php

namespace AbdulmajeedJamaan\FilamentTranslatableTabs;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

trait HasExtraConfigs
{
    private bool $handleRichEditor = false;

    public function addDirectionByLocale(): static
    {
        $this->modifyFieldsUsing(function (Field $component, string $locale) {
            $dir = str($locale)->startsWith('ar') ? 'rtl' : 'ltr';
            $component->extraAttributes(['style' => "direction: $dir;"], true);
        });

        return $this;
    }

    public function addConvertRichEditorEmptyPTagToNull(): static
    {
        $this->handleRichEditor = true;

        $this->modifyFieldsUsing(function (Field $component, string $locale) {
            if ($component instanceof RichEditor) {
                $component->dehydrateStateUsing(function ($state) {
                    return $state == '<p></p>' ? null : $state;
                });
            }
        });

        return $this;
    }

    public function addEmptyBadgeWhenAllFieldsAreEmpty(string $emptyLabel): static
    {
        $this->modifyTabsUsing(function (TranslatableTab $component, string $locale) use ($emptyLabel) {
            $hasValue = fn ($tab, $get): bool => collect($tab->getChildComponents())
                ->contains(function ($c) use ($get) {
                    $content = $get($c->getName());
                    if ($this->handleRichEditor && $c instanceof RichEditor) {
                        return $content != '<p></p>';
                    }
                    return ! empty($content);
                });

            $component
                ->live(true)
                ->badgeColor(fn ($component, $get) => $hasValue($component, $get) ? null : 'warning')
                ->badge(fn ($component, $get) => $hasValue($component, $get) ? null : $emptyLabel);
        });

        return $this;
    }

    public function addSetActiveTabThatHasValue(): static
    {
        $this->activeTab(function ($get, $component) {
            $hasValue = function ($tab, $get): bool {
                foreach ($tab->getChildComponents() as $component) {
                    $content = $get($component->getName());
                    if ($this->handleRichEditor && $component instanceof RichEditor) {
                        $html = RichContentRenderer::make($content)->toHtml();
                        if ($html != '<p></p>') {
                            return true;
                        }
                    } else if (! empty($content)) {
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
