<?php

declare(strict_types=1);

namespace App\Exception;

class ReaderServiceNotFoundException extends \RuntimeException
{
    public function __construct(string $message = "Reader service not implemented for given source.", int $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
