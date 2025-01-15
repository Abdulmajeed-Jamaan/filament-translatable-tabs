# Filament Translatable Tabs

[![Latest Version on Packagist](https://img.shields.io/packagist/v/abdulmajeed-jamaan/filament-translatable-tabs.svg?style=flat-square)](https://packagist.org/packages/abdulmajeed-jamaan/filament-translatable-tabs)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/abdulmajeed-jamaan/filament-translatable-tabs/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/abdulmajeed-jamaan/filament-translatable-tabs/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/abdulmajeed-jamaan/filament-translatable-tabs/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/abdulmajeed-jamaan/filament-translatable-tabs/actions?query=workflow%3A"fix+php+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/abdulmajeed-jamaan/filament-translatable-tabs.svg?style=flat-square)](https://packagist.org/packages/abdulmajeed-jamaan/filament-translatable-tabs)

Automatically generate tabs for translations.

This was created to be used with [spatie/laravel-translatable](https://github.com/spatie/laravel-translatable), but it can be used standalone as well.

![Preview](./art/single-field-preview.gif)

![Preview](./art/multiple-fields-preview.gif)

## Installation

You can install the package via composer:

```bash
composer require abdulmajeed-jamaan/filament-translatable-tabs
```

Then in any registered service provider `boot()` method configure the following:

```php
TranslatableTabs::configureUsing(function (TranslatableTabs $component) {
    $component
        // locales labels
        ->localesLabels([
            'ar' => __('locales.ar'),
            'en' => __('locales.en')
        ])
        // default locales
        ->locales(['ar', 'en']);
});
```

## Usage

```php
// Single Field
TextInput::make('title')
    ->translatableTabs();

// Multiple Fields
TranslatableTabs::make('anyLabel')
    ->schema([
        Forms\Components\TextInput::make("title"),
        Forms\Components\Textarea::make("content")
    ]);
```

## Customizations

You can customize [`Tab`](https://filamentphp.com/docs/3.x/forms/layout/tabs) and [`Field`](https://filamentphp.com/docs/3.x/forms/fields/getting-started) based on locale using the following methods:

```php
use AbdulmajeedJamaan\FilamentTranslatableTabs\TranslatableTabs;
use AbdulmajeedJamaan\FilamentTranslatableTabs\TranslatableTab;
use Filament\Forms\Components\Field;

$customizeTab = function (TranslatableTab $component, string $locale) {
    // ...
};

$customizeField = function (Field $component, string $locale) {
    // ...
};

// Globally in boot method
TranslatableTabs::configureUsing(function (TranslatableTabs $component) {
    $component
        ->modifyTabsUsing($customizeTab)
        ->modifyFieldsUsing($customizeField);
});

// Single Field
TextInput::make()
    ->translatableTabs(
        modifyTabsUsing: $customizeTab,
        modifyFieldsUsing: $customizeField
    )

// Multiple Fields
TranslatableTabs::make('anyLabel')
    ->modifyTabsUsing($customizeTab)
    ->modifyFieldsUsing($customizeField)
    ->schema([
        Forms\Components\TextInput::make("title"),
        Forms\Components\Textarea::make("content")
    ]);
```

#### Override the default locale:

You can add the method `locale` to change it on the fly:

```php
$localesFn = function () {
    return ['ar', 'en'];
    
    // also you can override label using:
    return [
        'ar' => 'Arabic',
        'en' => 'English'
    ]
}
// Single Field
TextInput::make('title')
    ->translatableTabs(locales: $localesFn);

// Multiple Fields
TranslatableTabs::make('anyLabel')
    ->locales($localesFn)
    ->schema([
        Forms\Components\TextInput::make("title"),
        Forms\Components\Textarea::make("content")
    ]);
```


### Pre made configurations

In order to have similar experience to the [preview](#filament-translatable-tabs) set the following:

```php
TranslatableTabs::configureUsing(function (TranslatableTabs $component) {
    $component
        ->addDirectionByLocale()
        ->addEmptyBadgeWhenAllFieldsAreEmpty(emptyLabel: __('locales.empty'))
        ->addSetActiveTabThatHasValue();
});
```


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Abdulmajeed-Jamaan](https://github.com/Abdulmajeed-Jamaan)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
