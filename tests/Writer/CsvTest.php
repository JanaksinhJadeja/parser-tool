<?php

namespace App\Tests\Writer;

use App\Tests\AppTestCase;
use App\Writer\Csv;

/**
 * @covers \App\Writer\Csv
 * @covers \App\AppManager
 * @covers \App\Util\Util
 */
class CsvTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCsvWriterDisplayDataOnScreenWithoutHeader()
    {
        $this->expectOutputRegex('/1,2/');
        $csvWriter = new Csv(['targetFile' => null, 'fixColumns' => null, 'columns' => ['test1' => 'test1', 'test2' => 'test2']]);
        $csvWriter->write(['test1' => 1, 'test2' => 2], 1);
    }

    public function testCsvWriterDisplayDataOnScreenWithHeader()
    {
        $this->expectOutputRegex('/test/');
        $csvWriter = new Csv(['targetFile' => null, 'fixColumns' => null, 'columns' => ['test1' => 'test1', 'test2' => 'test2']]);
        $csvWriter->write(['test1' => 1, 'test2' => 2], 0);
        $csvWriter->write(['test1' => 1, 'test2' => 2], 1);
    }

    public function testCsvWriterDisplayDataOnFileWitHeader()
    {
        $this->expectOutputRegex('/test/');
        $temp = tmpfile();
        $path = stream_get_meta_data($temp)['uri'];
        $csvWriter = new Csv(['targetFile' => $path, 'fixColumns' => null, 'columns' => ['test1' => 'test1', 'test2' => 'test2']]);
        $csvWriter->write(['test1' => 1, 'test2' => 2], 0);
        $csvWriter->write(['test1' => 1, 'test2' => 2], 1);
        echo readfile($path, 1024);
    }

    public function testCsvWriterDisplayDataOnFileWitHeaderWithColumn()
    {
        $this->expectOutputRegex('/^((?!XYZ).)*$/s');
        $temp = tmpfile();
        $path = stream_get_meta_data($temp)['uri'];
        $csvWriter = new Csv(['targetFile' => $path, 'fixColumns' => 'test1,test2', 'columns' => ['test1' => 'test1', 'test2' => 'test2', 'XYZ' => 'XYZ']]);
        $csvWriter->write(['test1' => 1, 'test2' => 2, 'XYZ' => 3], 0);
        $csvWriter->write(['test1' => 1, 'test2' => 2, 'XYZ' => 4], 1);
        echo readfile($path, 1024);
    }
}
