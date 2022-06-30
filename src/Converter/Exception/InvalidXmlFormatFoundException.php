<?php

declare(strict_types=1);

namespace App\Converter\Exception;

class InvalidXmlFormatFoundException extends \RuntimeException
{
    public function __construct(string $message = "Invalid XML format found.", int $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
