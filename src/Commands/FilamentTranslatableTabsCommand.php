<?php

namespace AbdulmajeedJamaan\FilamentTranslatableTabs\Commands;

use Illuminate\Console\Command;

class FilamentTranslatableTabsCommand extends Command
{
    public $signature = 'filament-translatable-tabs';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
