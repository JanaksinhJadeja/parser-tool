<?php

declare(strict_types=1);

namespace App\Converter\Exception;

use InvalidArgumentException;

class FileNotFoundOnUrlException extends InvalidArgumentException
{
    public function __construct(string $message = "File not found on Given URL", int $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
