<?php

declare(strict_types=1);

namespace App\Exception;

class FileNotFoundException extends \RuntimeException
{
    public function __construct(string $message = "File not found.", int $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
