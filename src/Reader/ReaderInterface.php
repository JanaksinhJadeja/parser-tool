<?php

declare(strict_types=1);

namespace App\Reader;

/**
 * Interface for Reader.
 */
interface ReaderInterface
{
    /**
     * Parse given file and push data to target writer.
     *
     * @return \Generator
     */
    public function parse(): \Generator;

    /**
     * Parse full file and prepare column keys which is used to generate other format.
     *
     * @return array
     */
    public function extractKeys(): array;
}
