<?php

namespace Fligno\FlignoToolkit\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

/**
 * Class EmptyPrivateTokenException
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class EmptyPrivateTokenException extends Exception
{
    #[Pure] public function __construct()
    {
        parent::__construct('Provided Gitlab Personal Access Token is empty!');
    }
}
