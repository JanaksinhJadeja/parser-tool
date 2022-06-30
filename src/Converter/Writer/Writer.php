<?php

declare(strict_types=1);

namespace App\Converter\Writer;

/**
 * Abstract class for Writer classes.
 */
abstract class Writer implements WriterInterface
{
    /**
     * Write data to target writer.
     *
     * @param array $dataArray
     * @param bool $firstWrite
     * @param array $columns
     * @return mixed
     */
    abstract public function writeData(array $dataArray, bool $firstWrite, array $columns): void;

    /**
     * Display final data on screen.
     *
     * @param array $dataArray
     * @param bool $firstWrite
     * @param array $columns
     * @return void
     */
    abstract protected function display(array $dataArray, bool $firstWrite, array $columns): void;

    /**
     * Write data on target.
     *
     * @param array $dataArray
     * @param bool $firstWrite
     * @param array $columns
     * @return void
     * @throws \League\Csv\CannotInsertRecord
     */
    abstract protected function write(array $dataArray, bool $firstWrite, array $columns): void;
}
