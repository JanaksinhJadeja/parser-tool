<?php

declare(strict_types=1);

namespace App\Writer;

/**
 * Interface for Writer classes.
 */
interface WriterInterface
{
    public function write(array $dataArray, int $index): void;
}
