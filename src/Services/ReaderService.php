<?php

declare(strict_types=1);

namespace App\Services;

use App\Reader\ReaderInterface;

/**
 * Reader Service
 */
class ReaderService
{
    private ReaderInterface $reader;

    public function setReader(ReaderInterface $reader): self
    {
        $this->reader = $reader;
        return $this;
    }

    public function parse(): \Generator
    {
        return  $this->reader->parse();
    }

    public function extractKeys(): array
    {
        return $this->reader->extractKeys();
    }
}
