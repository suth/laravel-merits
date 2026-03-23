<?php

namespace Suth\Merits\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Suth\Merits\Merits
 */
class Merits extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Suth\Merits\Merits::class;
    }
}
