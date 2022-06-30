<?php

declare(strict_types=1);

namespace App\Converter\Writer;

use App\AppManagerInterface;

/**
 * Primary purpose of Sqlite writer is to convert array to sql format and write target or display on screen.
 */
class SqliteWriter extends Writer
{
    private AppManagerInterface $appManager;

    private string $targetFile;

    /**
     * Initialize Sqlite Writer.
     *
     * @param AppManagerInterface $appManager
     * @param string $targetFile
     */
    public function __construct(AppManagerInterface $appManager, string $targetFile = 'screen')
    {
        $this->appManager = $appManager;
        $this->targetFile = $targetFile;
    }

    /**
     * @inheritDoc
     */
    public function writeData(array $dataArray, bool $firstWrite, array $columns): void
    {
        // TODO: Implement writeData() method.
    }

    /**
     * @inheritDoc
     */
    protected function display(array $dataArray, bool $firstWrite, array $columns): void
    {
        // TODO: Implement display() method.
    }

    /**
     * @inheritDoc
     */
    protected function write(array $dataArray, bool $firstWrite, array $columns): void
    {
        // TODO: Implement write() method.
    }
}
