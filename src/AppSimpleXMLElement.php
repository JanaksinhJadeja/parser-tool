<?php

declare(strict_types=1);

namespace App;

class AppSimpleXMLElement
{
    public function getStringValue($value)
    {
        $string = trim((string) $value);
        if ($string) {
            return $string;
        } else {
            return null;
        }
    }
}
