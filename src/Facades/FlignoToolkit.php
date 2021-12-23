<?php

namespace Fligno\FlignoToolkit\Facades;

use Illuminate\Support\Facades\Facade;

class FlignoToolkit extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'fligno-toolkit';
    }
}
