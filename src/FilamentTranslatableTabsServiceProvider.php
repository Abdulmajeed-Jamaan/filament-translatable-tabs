<?php

namespace AbdulmajeedJamaan\FilamentTranslatableTabs;

use AbdulmajeedJamaan\FilamentTranslatableTabs\Commands\FilamentTranslatableTabsCommand;
use AbdulmajeedJamaan\FilamentTranslatableTabs\Testing\TestsFilamentTranslatableTabs;
use Closure;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTranslatableTabsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-translatable-tabs';

    public static string $viewNamespace = 'filament-translatable-tabs';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->askToStarRepoOnGitHub('abdulmajeed-jamaan/filament-translatable-tabs');
            });
    }

    public function packageRegistered(): void
    {
    }

    public function packageBooted(): void
    {
        Field::macro('translatableTabs', function (
            Closure|array|null $locales = null,
            ?Closure           $modifyTabsUsing = null,
            ?Closure           $modifyFieldsUsing = null
        ) {
            /**
             * @var Field $this
             */
            return TranslatableTabs::make($this->getLabel())
                ->locales($locales)
                ->modifyTabsUsing($modifyTabsUsing)
                ->modifyFieldsUsing($modifyFieldsUsing)
                ->schema([$this]);
        });
    }

    protected function getAssetPackageName(): ?string
    {
        return 'abdulmajeed-jamaan/filament-translatable-tabs';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [];
    }
}
