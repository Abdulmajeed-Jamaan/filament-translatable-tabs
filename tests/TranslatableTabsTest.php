<?php

use AbdulmajeedJamaan\FilamentTranslatableTabs\Tests\Fixtures\TestForm;
use AbdulmajeedJamaan\FilamentTranslatableTabs\TranslatableTab;
use AbdulmajeedJamaan\FilamentTranslatableTabs\TranslatableTabs;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

/**
 * @param  Closure(): array<Component>  $components
 * @param  array<string, mixed>  $state
 */
function renderForm(Closure $components, array $state = []): Testable
{
    TestForm::$components = $components;
    TestForm::$initialState = $state;

    return Livewire::test(TestForm::class);
}

function findTranslatableTabs(Testable $livewire): TranslatableTabs
{
    return collect($livewire->instance()->form->getComponents())
        ->first(fn ($component) => $component instanceof TranslatableTabs);
}

beforeEach(function () {
    TranslatableTabs::configureUsing(function (TranslatableTabs $component) {
        $component
            ->localesLabels([
                'ar' => 'Arabic',
                'en' => 'English',
            ])
            ->locales(['ar', 'en']);
    });
});

it('turns a single field into translatable tabs', function () {
    expect(TextInput::make('title')->translatableTabs())
        ->toBeInstanceOf(TranslatableTabs::class);

    renderForm(fn () => [TextInput::make('title')->translatableTabs()])
        ->assertSee('Arabic')
        ->assertSee('English')
        ->assertFormFieldExists('title.ar')
        ->assertFormFieldExists('title.en');
});

it('stores each locale under the field state path', function () {
    renderForm(fn () => [TextInput::make('title')->translatableTabs()])
        ->fillForm([
            'title' => [
                'ar' => 'مرحبا',
                'en' => 'Hello',
            ],
        ])
        ->assertFormSet([
            'title.ar' => 'مرحبا',
            'title.en' => 'Hello',
        ])
        ->assertSet('data.title', [
            'ar' => 'مرحبا',
            'en' => 'Hello',
        ]);
});

it('fills existing translations into each tab', function () {
    renderForm(
        fn () => [TextInput::make('title')->translatableTabs()],
        ['title' => ['ar' => 'مرحبا', 'en' => 'Hello']],
    )
        ->assertFormSet([
            'title.ar' => 'مرحبا',
            'title.en' => 'Hello',
        ]);
});

it('groups multiple fields under the same tabs', function () {
    $livewire = renderForm(fn () => [
        TranslatableTabs::make('Translations')
            ->schema([
                TextInput::make('title'),
                Textarea::make('content'),
            ]),
    ])
        ->assertFormFieldExists('title.ar')
        ->assertFormFieldExists('title.en')
        ->assertFormFieldExists('content.ar')
        ->assertFormFieldExists('content.en');

    expect(findTranslatableTabs($livewire)->getChildComponents())
        ->toHaveCount(2)
        ->each->toBeInstanceOf(TranslatableTab::class);
});

it('overrides the default locales with labels', function () {
    renderForm(fn () => [
        TextInput::make('title')
            ->translatableTabs()
            ->locales(fn () => ['fr' => 'French', 'de' => 'German']),
    ])
        ->assertSee('French')
        ->assertSee('German')
        ->assertDontSee('Arabic')
        ->assertFormFieldExists('title.fr')
        ->assertFormFieldExists('title.de')
        ->assertFormFieldDoesNotExist('title.ar');
});

it('overrides the default locales using the configured labels', function () {
    renderForm(fn () => [
        TextInput::make('title')->translatableTabs(locales: ['en']),
    ])
        ->assertSee('English')
        ->assertDontSee('Arabic')
        ->assertFormFieldExists('title.en')
        ->assertFormFieldDoesNotExist('title.ar');
});

it('customizes tabs and fields per locale', function () {
    renderForm(fn () => [
        TextInput::make('title')
            ->translatableTabs()
            ->modifyTabsUsing(fn (TranslatableTab $component, string $locale) => $component->label("Tab {$locale}"))
            ->modifyFieldsUsing(fn (Field $component, string $locale) => $component->placeholder("Title in {$locale}")),
    ])
        ->assertSee('Tab ar')
        ->assertSee('Tab en')
        ->assertSee('Title in ar')
        ->assertSee('Title in en');
});

it('applies customizations configured globally', function () {
    TranslatableTabs::configureUsing(function (TranslatableTabs $component) {
        $component
            ->modifyTabsUsing(fn (TranslatableTab $component, string $locale) => $component->label("Global tab {$locale}"))
            ->modifyFieldsUsing(fn (Field $component, string $locale) => $component->placeholder("Global {$locale}"));
    });

    renderForm(fn () => [TextInput::make('title')->translatableTabs()])
        ->assertSee('Global tab ar')
        ->assertSee('Global en');
});

it('only accepts fields as children', function () {
    TranslatableTabs::make('Translations')
        ->schema([Section::make('Not a field')])
        ->getDefaultChildComponents();
})->throws(RuntimeException::class, 'Only instances of type');

it('sets the text direction by locale', function () {
    renderForm(fn () => [
        TextInput::make('title')
            ->translatableTabs()
            ->addDirectionByLocale(),
    ])
        ->assertSeeHtml('direction: rtl;')
        ->assertSeeHtml('direction: ltr;');
});

it('marks tabs whose fields are all empty', function () {
    $components = fn () => [
        TextInput::make('title')
            ->translatableTabs()
            ->addEmptyBadgeWhenAllFieldsAreEmpty(emptyLabel: 'Missing translation'),
    ];

    renderForm($components, ['title' => ['ar' => null, 'en' => 'Hello']])
        ->assertSeeTextInOrder(['Arabic', 'Missing translation', 'English']);

    renderForm($components, ['title' => ['ar' => 'مرحبا', 'en' => 'Hello']])
        ->assertDontSee('Missing translation');
});

it('opens the first tab that has a value', function () {
    $components = fn () => [
        TextInput::make('title')
            ->translatableTabs()
            ->addSetActiveTabThatHasValue(),
    ];

    $livewire = renderForm($components, ['title' => ['ar' => null, 'en' => 'Hello']]);
    expect(findTranslatableTabs($livewire)->getActiveTab())->toBe(2);

    $livewire = renderForm($components);
    expect(findTranslatableTabs($livewire)->getActiveTab())->toBe(1);
});
