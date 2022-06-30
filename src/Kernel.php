<?php

namespace App;

use Symfony\Component\Console\Application;

/**
 * Application Kernel class.
 */
final class Kernel extends Application
{
    public function __construct(iterable $commands = [], string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        foreach ($commands as $command) {
            $this->add($command);
        }
        parent::__construct($name, $version);
    }
}
