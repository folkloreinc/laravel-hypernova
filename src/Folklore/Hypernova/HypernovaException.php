<?php

namespace Folklore\Hypernova;

use Throwable;

class HypernovaException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct('Hypernova Exception: ' . $message, $code, $previous);
    }
}
