<?php

declare(strict_types=1);

namespace App\Services;

use App\Writer\WriterInterface;

/**
 * Writer Service
 */
class WriterService
{
    private WriterInterface $writer;

    public function setWriter(WriterInterface $writer): self
    {
        $this->writer = $writer;
        return $this;
    }

    public function write(array $data, int $index): void
    {
        $this->writer->write($data, $index);
    }
}
