<?php

namespace App\Tests\Reader;

use App\Exception\FileNotFoundException;
use App\Exception\InvalidXmlFormatFoundException;
use App\Reader\Xml;
use App\Tests\AppTestCase;

/**
 * @covers \App\Util\Util
 * @covers \App\Exception\FileNotFoundException
 * @covers \App\Reader\Xml
 * @covers \App\AppManager
 * @covers \App\Exception\InvalidXmlFormatFoundException
 *
 */
class XmlTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testXmlReaderShouldReturnFileNotFoundOnFilePathNotGiven()
    {
        $this->expectException(FileNotFoundException::class);
        $xmlReader = new Xml();
    }

    public function testXmlReaderShouldGenerateRuntimeExceptionOnKeyNodeIsNotGiven()
    {
        $this->expectException(\RuntimeException::class);
        $xml = $this->getCorrectXMLString();
        $temp = tmpfile();
        fwrite($temp, $xml);
        $path = stream_get_meta_data($temp)['uri'];
        $xmlReader = new Xml(['filePath' => $path]);
    }

    public function testParseReturnsCorrectData()
    {
        $xml = $this->getCorrectXMLString();
        $temp = tmpfile();
        fwrite($temp, $xml);
        $path = stream_get_meta_data($temp)['uri'];
        $xmlReader = new Xml(['filePath' => $path, 'keyNode' => 'Merchant']);
        $dataArray = [];
        foreach ($xmlReader->parse() as $data) {
            $dataArray [] = $data;
        }
        $this->assertContains('ABC', $dataArray[2]);
    }

    public function testExtractKeysCorrectData()
    {
        $xml = $this->getCorrectXMLString();
        $temp = tmpfile();
        fwrite($temp, $xml);
        $path = stream_get_meta_data($temp)['uri'];
        $xmlReader = new Xml(['filePath' => $path, 'keyNode' => 'Merchant']);
        $dataArray = [];
        $dataArray = $xmlReader->extractKeys();
        $this->assertContains('merchantID', $dataArray);
    }

    public function testParseReturnsExceptionOnInCorrectData()
    {
        $this->expectException(InvalidXmlFormatFoundException::class);
        $xml = $this->getWrongXMLString();
        $temp = tmpfile();
        fwrite($temp, $xml);
        $path = stream_get_meta_data($temp)['uri'];
        $xmlReader = new Xml(['filePath' => $path, 'keyNode' => 'Merchant']);
        $dataArray = [];
        $dataArray = $xmlReader->extractKeys();
        $this->assertContains('merchantID', $dataArray);
    }
}
