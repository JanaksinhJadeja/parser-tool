<?php

declare(strict_types=1);

namespace App\Exception;

class WriterServiceNotFoundException extends \RuntimeException
{
    public function __construct(string $message = "Writer service not implemented for given target.", int $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
