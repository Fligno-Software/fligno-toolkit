<?php

namespace Fligno\FlignoToolkit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class FlignoToolkit
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 *
 * @see \Fligno\FlignoToolkit\FlignoToolkit
 */
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
