<?php

/**
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 * @since 2021-12-20
 */

use Fligno\FlignoToolkit\FlignoToolkit;

if (! function_exists('flignoToolkit'))
{
    function flignoToolkit(): FlignoToolkit
    {
        return resolve('fligno-toolkit');
    }
}

if (! function_exists('fligno_toolkit'))
{
    function fligno_toolkit(): FlignoToolkit
    {
        return flignoToolkit();
    }
}
