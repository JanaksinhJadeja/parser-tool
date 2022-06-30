<?php

namespace App\Tests\Converter\Parser;

use App\Converter\Exception\FileNotFoundException;
use App\Converter\Exception\InvalidXmlFormatFoundException;
use App\Converter\Parser\XmlParser;
use App\Converter\Writer\CsvWriter;
use App\Converter\Writer\WriterInterface;
use App\Tests\AppTestCase;

/**
 * @covers \App\Converter\Parser\XmlParser
 * @covers \App\Util\Util
 * @covers \App\Converter\Writer\CsvWriter
 * @covers \App\AppManager
 * @covers \App\Converter\Exception\InvalidXmlFormatFoundException
 * @covers \App\Converter\Exception\FileNotFoundException
 */
class XmlParserTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->csvWriter = new CsvWriter($this->appManager, 'screen', true);
    }

    public function testXmlParserPreparesAllKeysAndReturnCorrectData()
    {
        $this->csvWriter = new CsvWriter($this->appManager);
        $xml = $this->getCorrectXMLString();
        $temp = tmpfile();
        fwrite($temp, $xml);
        $path = stream_get_meta_data($temp)['uri'];
        $this->assertInstanceOf(WriterInterface::class, $this->csvWriter);
        $xmlParser = new XmlParser($this->csvWriter, $path);
        $keys = $xmlParser->prepareAllKeys('Merchant');
        $this->assertEquals(3, count($keys));
        fclose($temp);
    }

    public function testXmlParserWriteDataOnScreenWithoutHeader()
    {
        $this->csvWriter = new CsvWriter($this->appManager);
        $xml = $this->getCorrectXMLString();
        $temp = tmpfile();
        fwrite($temp, $xml);
        $path = stream_get_meta_data($temp)['uri'];
        $this->assertInstanceOf(WriterInterface::class, $this->csvWriter);
        $xmlParser = new XmlParser($this->csvWriter, $path);
        $columns = $xmlParser->prepareAllKeys('Merchant');
        $this->expectOutputRegex('/merchantID/');
        $this->expectOutputRegex('/ABC/');
        $xmlParser->parseAndPushData('Merchant', $columns);
        fclose($temp);
    }

    public function testXmlParserWithWrongData()
    {
        $this->csvWriter = new CsvWriter($this->appManager);
        $xml = $this->getWrongXMLString();
        $temp = tmpfile();
        fwrite($temp, $xml);
        $path = stream_get_meta_data($temp)['uri'];
        $this->expectException(InvalidXmlFormatFoundException::class);
        $xmlParser = new XmlParser($this->csvWriter, $path);
        $columns = [];
        $xmlParser->parseAndPushData('Merchant', $columns);
        fclose($temp);
    }

    public function testXmlParserParseAndPushDataWithNoFile()
    {
        $this->csvWriter = new CsvWriter($this->appManager);
        $path = '/tmp/wrong.xml';
        $this->expectException(FileNotFoundException::class);
        $xmlParser = new XmlParser($this->csvWriter, $path);
        $columns = [];
        $xmlParser->parseAndPushData('Merchant', $columns);
    }
}
