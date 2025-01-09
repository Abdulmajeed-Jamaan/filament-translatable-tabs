<?php

namespace AbdulmajeedJamaan\FilamentTranslatableTabs;

use AbdulmajeedJamaan\FilamentTranslatableTabs\Commands\FilamentTranslatableTabsCommand;
use AbdulmajeedJamaan\FilamentTranslatableTabs\Testing\TestsFilamentTranslatableTabs;
use Filament\Support\Assets\AlpineComponent;
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
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('abdulmajeed-jamaan/filament-translatable-tabs');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-translatable-tabs/{$file->getFilename()}"),
                ], 'filament-translatable-tabs-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsFilamentTranslatableTabs);
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
        return [
            // AlpineComponent::make('filament-translatable-tabs', __DIR__ . '/../resources/dist/components/filament-translatable-tabs.js'),
            Css::make('filament-translatable-tabs-styles', __DIR__ . '/../resources/dist/filament-translatable-tabs.css'),
            Js::make('filament-translatable-tabs-scripts', __DIR__ . '/../resources/dist/filament-translatable-tabs.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentTranslatableTabsCommand::class,
        ];
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
        return [
            'create_filament-translatable-tabs_table',
        ];
    }
}
