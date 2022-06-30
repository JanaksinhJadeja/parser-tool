<?php

declare(strict_types=1);

namespace App\Converter\Parser;

use App\Converter\Writer\WriterInterface;

/**
 * Interface for Parser.
 */
interface ParserInterface
{
    /**
     * Initialize Parser
     *
     * @param WriterInterface $targetWriter
     * @param string $filePath
     */
    public function __construct(WriterInterface $targetWriter, string $filePath);

    /**
     * Parse given file and push data to target writer.
     *
     * @param string $keyNode
     * @param array $columns
     * @param int $limit
     * @param $encoding
     * @param $noHeading
     * @return mixed
     */
    public function parseAndPushData(string $keyNode, array $columns, int $limit = 0, $encoding = 'UTF-8', $noHeading = false);

    /**
     * Parse full file and prepare column keys which is used to generate other format.
     *
     * @param $keyNode
     * @param int $limit
     * @return array
     */
    public function prepareAllKeys($keyNode, int $limit = 0): array;
}
