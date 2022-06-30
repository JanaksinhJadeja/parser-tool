<?php

namespace App\Tests\Converter\Writer;

use App\AppManager;
use App\AppManagerInterface;
use App\Converter\Writer\CsvWriter;
use App\Converter\Writer\WriterInterface;
use App\Tests\AppTestCase;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Converter\Writer\CsvWriter
 * @covers \App\AppManager
 * @covers \App\Util\Util
 */
class CsvWriterTest extends AppTestCase
{
    private WriterInterface $csvWriter;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCsvWriterObjectCreatedSuccessfully()
    {
        $this->csvWriter = new CsvWriter($this->appManager);
        $this->assertInstanceOf(WriterInterface::class, $this->csvWriter);
    }

    public function testCsvWriterDisplayDataOnFileWithoutHeader()
    {
        $this->expectOutputRegex('/1,2,3/');
        $temp = tmpfile();
        $path = stream_get_meta_data($temp)['uri'];
        $this->csvWriter = new CsvWriter($this->appManager, $path, true);
        $this->csvWriter->writeData([['1', '2', '3']], false, ['test1', 'test2']);
        echo readfile($path, 1024);
    }

    public function testCsvWriterDisplayDataOnFileWitHeader()
    {
        $this->expectOutputRegex('/test/');
        $temp = tmpfile();
        $path = stream_get_meta_data($temp)['uri'];
        $this->csvWriter = new CsvWriter($this->appManager, $path, false);
        $this->csvWriter->writeData([['1', '2', '3']], true, ['test1' => null, 'test2' => null, 'test3' => '']);
        echo readfile($path, 1024);
    }

    public function testCsvWriterDisplayDataOnFileWitHeaderWithColumn()
    {
        $this->expectOutputRegex('/^((?!XYZ).)*$/s');
        $temp = tmpfile();
        $path = stream_get_meta_data($temp)['uri'];
        $this->csvWriter = new CsvWriter($this->appManager, $path, false, ['test3']);
        $this->csvWriter->writeData([['1', '2', '3']], true, ['test1' => null, 'XYZ' => null, 'test3' => '']);
        echo readfile($path, 1024);
    }

    public function testCsvWriterDisplayDataOnScreenWithoutHeader()
    {
        $this->csvWriter = new CsvWriter($this->appManager, 'screen', true);
        $this->expectOutputRegex('/1,2,3/');
        $this->csvWriter->writeData([['1', '2', '3']], false, ['test1', 'test2']);
    }

    public function testCsvWriterWriteDataOnScreenWithHeader()
    {
        $this->csvWriter = new CsvWriter($this->appManager, 'screen', false);
        $this->expectOutputRegex('/test/');
        $this->csvWriter->writeData([['1', '2', '3']], true, ['test1' => null, 'test2' => null, 'test3' => '']);
    }

    public function testCsvWriterWriteDataOnScreenWithHeaderWithColumn()
    {
        $this->csvWriter = new CsvWriter($this->appManager, 'screen', false, ['test3']);
        $this->expectOutputRegex('/^((?!XYZ).)*$/s');
        $this->csvWriter->writeData([['1', '2', '3']], true, ['test1' => null, 'XYZ' => null, 'test3' => '']);
    }

}
