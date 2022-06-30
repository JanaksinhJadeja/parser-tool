<?php

namespace App\Command;

use App\AppManager;
use App\Tests\AppTestCase;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \App\Command\XmlToCSVCommand
 * @covers \App\AppManager
 * @covers \App\Converter\Writer\CsvWriter
 * @covers \App\Converter\Parser\XmlParser
 * @covers \App\Util\Util
 * @covers \App\Converter\Exception\FileNotFoundOnUrlException
 * @covers \App\Converter\Exception\InvalidXmlFormatFoundException
 * @covers \App\Converter\Exception\FileNotFoundException
 */
class XmlToCsvCommandTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testXmlToCsvCommandGivesRuntimeExceptionOnNoArguments()
    {
        $this->expectException(RuntimeException::class);
        $application = new Application();
        $application->add(new XmlToCSVCommand($this->appManager, 'ps:xmltocsv'));
        $command = $application->find('ps:xmltocsv');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);
    }


    public function testXmlToCsvCommandGivesCorrectOutput()
    {
        $application = new Application();
        $application->add(new XmlToCSVCommand($this->appManager, 'ps:xmltocsv'));
        $command = $application->find('ps:xmltocsv');
        $commandTester = new CommandTester($command);
        $this->expectOutputRegex('/name,price,description,calories/');
        $commandTester->execute(['infile' => 'https://www.w3schools.com/xml/simple.xml', 'key' => 'food', '--limit' => 2]);
        $commandTester->assertCommandIsSuccessful();
    }

    public function testXmlToCsvCommandGivesCorrectReturnOnWrongUrlFile()
    {
        $application = new Application();
        $application->add(new XmlToCSVCommand($this->appManager, 'ps:xmltocsv'));
        $command = $application->find('ps:xmltocsv');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['infile' => 'https://www.w3schools.com/xml/simple2.xml', 'key' => 'food', '--limit' => 2]);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testXmlToCsvCommandWithCorrectFileData()
    {
        $xml = $this->getCorrectXMLString();
        $temp = tmpfile();
        fwrite($temp, $xml);
        $path = stream_get_meta_data($temp)['uri'];

        $this->expectOutputRegex('/merchantID,subID/');
        $application = new Application();
        $application->add(new XmlToCSVCommand($this->appManager, 'ps:xmltocsv'));
        $command = $application->find('ps:xmltocsv');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['infile' => $path, 'key' => 'Merchant', '--limit' => 2]);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testXmlToCsvCommandWithWrongFileData()
    {
        $xml = $this->getWrongXMLString();
        $temp = tmpfile();
        fwrite($temp, $xml);
        $path = stream_get_meta_data($temp)['uri'];

        $application = new Application();
        $application->add(new XmlToCSVCommand($this->appManager, 'ps:xmltocsv'));
        $command = $application->find('ps:xmltocsv');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['infile' => $path, 'key' => 'Merchant', '--limit' => 2]);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testXmlToCsvCommandWithNoFileData()
    {
        $application = new Application();
        $application->add(new XmlToCSVCommand($this->appManager, 'ps:xmltocsv'));
        $command = $application->find('ps:xmltocsv');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['infile' => '/tmp/xyz.xml', 'key' => 'Merchant', '--limit' => 2]);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }
}
