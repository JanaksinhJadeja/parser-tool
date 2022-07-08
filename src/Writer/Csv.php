<?php

declare(strict_types=1);

namespace App\Writer;

use App\Util\Util;

/**
 * Primary purpose of CSV writer is to convert array to csv format and write target or display on screen.
 */
class Csv implements WriterInterface
{
    private string $targetFile;

    private array $fixColumns;

    private array $columns;

    public function __construct(array $options = [])
    {
        $this->targetFile = $options['targetFile'] ?? 'screen';
        $this->fixColumns = $options['fixColumns'] != null ? explode(',', $options['fixColumns']) : [];
        $this->columns    = $options['columns'] ?? [];
    }

    private function createFile(bool $firstWrite): \League\Csv\Writer
    {
        $writer = \League\Csv\Writer::createFromPath($this->targetFile, $firstWrite ? 'w+' : 'a+');
        if ($this->fixColumns == null && $firstWrite) {
            $writer->insertOne($this->columns);
        } else {
            $writer->insertOne($this->fixColumns);
        }


        return $writer;
    }

    protected function display(array $dataArray, int $index): void
    {
        if ($this->fixColumns == null && $index == 0) {
            echo implode(',', $this->columns)."\n";
        } elseif ($index == 0) {
            echo implode(',', $this->fixColumns)."\n";
        }
        echo implode(',', $dataArray)."\n";
    }

    protected function writeFile($data, int $index): void
    {
        $writer = $this->createFile($index == 0);
        $writer->insertOne($data);
    }

    private function formatData(array $dataArray): array
    {
        if ($this->fixColumns == null) {
            return array_replace($this->columns, Util::makeArrayFlat($dataArray));
        } else {
            $elements = Util::makeArrayFlat($dataArray);
            $trimmedRow = [];
            foreach ($this->fixColumns as $fVal) {
                $trimmedRow[$fVal] = $elements[$fVal] ?? null;
            }
            return $trimmedRow;
        }
    }

    public function write(array $dataArray, int $index): void
    {
        $data = $this->formatData($dataArray);

        if (count($data)) {
            if ($this->targetFile != 'screen') {
                $this->writeFile($data, $index);
            } else {
                $this->display($data, $index);
            }
        }
    }
}
