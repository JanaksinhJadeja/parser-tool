<?php

declare(strict_types=1);

namespace App\Converter\Writer;

use App\AppManagerInterface;
use App\Util\Util;

/**
 * Primary purpose of CSV writer is to convert array to csv format and write target or display on screen.
 */
class CsvWriter extends Writer
{
    private AppManagerInterface $appManager;

    private string $targetFile;

    private bool $noHeading;

    private array $fixColumns;

    /**
     * Initialize CSV writer.
     *
     * @param AppManagerInterface $appManager
     * @param string $targetFile
     * @param bool $noHeading
     */
    public function __construct(AppManagerInterface $appManager, string $targetFile = 'screen', bool $noHeading = false, $fixColumns=[])
    {
        $this->appManager = $appManager;
        $this->targetFile = $targetFile;
        $this->noHeading  = $noHeading;
        $this->fixColumns = $fixColumns;
    }

    /**
     * @inheritDoc
     */
    protected function display(array $dataArray, bool $firstWrite, array $columns): void
    {
        if ($this->noHeading === false && $firstWrite) {
            $header = array_combine(array_keys($columns), array_keys($columns));
            if ($this->fixColumns && count($this->fixColumns) > 0) {
                echo implode(',', array_intersect($this->fixColumns, $header))."\n";
            } else {
                echo implode(',', $header)."\n";
            }
        }

        foreach ($dataArray as $row) {
            if ($this->fixColumns && count($this->fixColumns) > 0) {
                $elements = Util::makeArrayFlat($row);
                $trimmedRow = [];
                foreach ($this->fixColumns as $fVal) {
                    $trimmedRow[$fVal] = $elements[$fVal] ?? null;
                }

                echo implode(',', $trimmedRow)."\n";
            } else {
                echo implode(',', array_replace($columns, Util::makeArrayFlat($row)))."\n";
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function write(array $dataArray, bool $firstWrite, array $columns): void
    {
        $writer = \League\Csv\Writer::createFromPath($this->targetFile, $firstWrite ? 'w+' : 'a+');
        $data = [];

        if ($this->noHeading === false && $firstWrite) {
            $header = array_combine(array_keys($columns), array_keys($columns));
            if ($this->fixColumns && count($this->fixColumns) > 0) {
                $header = array_intersect($this->fixColumns, $header);
            }
            $writer->insertOne($header);
        }
        foreach ($dataArray as $row) {
            if ($this->fixColumns && count($this->fixColumns) > 0) {
                $elements = Util::makeArrayFlat($row);
                $trimmedRow = [];
                foreach ($this->fixColumns as $fVal) {
                    $trimmedRow[$fVal] = $elements[$fVal] ?? null;
                }
                $data[] = $trimmedRow;
            } else {
                $data[] = array_replace($columns, Util::makeArrayFlat($row));
            }
        }

        $writer->insertAll($data);
    }

    /**
     * @inheritDoc
     */
    public function writeData(array $dataArray, bool $firstWrite, array $columns): void
    {
        if (count($dataArray)) {
            if ($this->targetFile != 'screen') {
                $this->write($dataArray, $firstWrite, $columns);
            } else {
                $this->display($dataArray, $firstWrite, $columns);
            }
        }
    }
}
