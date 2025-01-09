<?php

namespace AbdulmajeedJamaan\FilamentTranslatableTabs\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AbdulmajeedJamaan\FilamentTranslatableTabs\FilamentTranslatableTabs
 */
class FilamentTranslatableTabs extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \AbdulmajeedJamaan\FilamentTranslatableTabs\FilamentTranslatableTabs::class;
    }
}
