<?php

namespace AbdulmajeedJamaan\FilamentTranslatableTabs;

use Closure;
use Filament\Forms\Components\Field;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTranslatableTabsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-translatable-tabs';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->askToStarRepoOnGitHub('abdulmajeed-jamaan/filament-translatable-tabs');
            });
    }

    public function packageBooted(): void
    {
        Field::macro('translatableTabs', function (
            Closure | array | null $locales = null,
            ?Closure $modifyTabsUsing = null,
            ?Closure $modifyFieldsUsing = null
        ) {
            /**
             * @var Field $this
             */
            return TranslatableTabs::make($this->getLabel())
                ->when(! is_null($locales), fn (TranslatableTabs $tabs) => $tabs->locales($locales))
                ->when(! is_null($modifyTabsUsing), fn (TranslatableTabs $tabs) => $tabs->modifyTabsUsing($modifyTabsUsing))
                ->when(! is_null($modifyFieldsUsing), fn (TranslatableTabs $tabs) => $tabs->modifyFieldsUsing($modifyFieldsUsing))
                ->schema([$this]);
        });
    }
}
