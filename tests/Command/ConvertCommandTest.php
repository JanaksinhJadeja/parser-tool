<?php

namespace App\Command;

use App\Exception\FileNotFoundException;
use App\Exception\FileNotFoundOnUrlException;
use App\Services\FileService;
use App\Services\ReaderService;
use App\Services\WriterService;
use App\Tests\AppTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \App\Command\ConvertCommand
 * @covers \App\AppManager
 * @covers \App\Util\Util
 * @covers \App\Reader\Xml
 * @covers \App\Writer\Csv
 * @covers \App\Services\FileService
 * @covers \App\Services\ReaderService
 * @covers \App\Services\WriterService
 * @covers \App\Exception\FileNotFoundException
 * @covers \App\Exception\FileNotFoundOnUrlException
 * @covers \App\Exception\InvalidXmlFormatFoundException
 * @covers \App\Exception\ReaderServiceNotFoundException
 * @covers \App\Exception\WriterServiceNotFoundException
 */
class ConvertCommandTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->writerService  = new WriterService();
        $this->readerService  = new ReaderService();
        $this->fileService    = new FileService();
    }

    public function testConvertCommandGivesRuntimeExceptionOnNoArguments()
    {
        $this->expectException(RuntimeException::class);
        $application = new Application();
        $application->add(new ConvertCommand($this->writerService, $this->readerService, $this->fileService, $this->logger, 'ps:convert'));
        $command = $application->find('ps:convert');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);
    }

    public function testWrongSourceGivesException()
    {
        $application = new Application();
        $application->add(new ConvertCommand($this->writerService, $this->readerService, $this->fileService, $this->logger, 'ps:convert'));
        $command = $application->find('ps:convert');
        $commandTester = new CommandTester($command);
        $xml = $this->getCorrectXMLString();
        $temp = tmpfile();
        fwrite($temp, $xml);
        $path = stream_get_meta_data($temp)['uri'];
        $commandTester->execute(
            [
                '--source' => 'json',
                '--target' => 'csv',
                '--source_type' => 'local',
                'infile' => $path,
                '--key' => 'food',
                '--limit' => 2
            ]
        );
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testWrongTargetGivesException()
    {
        $application = new Application();
        $application->add(new ConvertCommand($this->writerService, $this->readerService, $this->fileService, $this->logger, 'ps:convert'));
        $command = $application->find('ps:convert');
        $commandTester = new CommandTester($command);
        $xml = $this->getCorrectXMLString();
        $temp = tmpfile();
        fwrite($temp, $xml);
        $path = stream_get_meta_data($temp)['uri'];
        $commandTester->execute(
            [
                '--source' => 'xml',
                '--target' => 'json',
                '--source_type' => 'local',
                'infile' => $path,
                '--key' => 'food',
                '--limit' => 2
            ]
        );
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testXmlToCsvCommandGivesCorrectOutput()
    {
        $application = new Application();
        $application->add(new ConvertCommand($this->writerService, $this->readerService, $this->fileService, $this->logger, 'ps:convert'));
        $command = $application->find('ps:convert');
        $commandTester = new CommandTester($command);
        $this->expectOutputRegex('/name,price,description,calories/');
        $commandTester->execute(
            [
                '--source' => 'xml',
                '--target' => 'csv',
                '--source_type' => 'remote',
                'infile' => 'https://www.w3schools.com/xml/simple.xml',
                '--key' => 'food',
                '--limit' => 2
            ]
        );
    }

    public function testXmlToCsvCommandGivesCorrectReturnOnWrongUrlFile()
    {
        $this->expectException(FileNotFoundOnUrlException::class);
        $application = new Application();
        $application->add(new ConvertCommand($this->writerService, $this->readerService, $this->fileService, $this->logger, 'ps:convert'));
        $command = $application->find('ps:convert');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                '--source' => 'xml',
                '--target' => 'csv',
                '--source_type' => 'remote',
                'infile' => 'https://www.w3schools.com/xml/simple2.xml',
                '--key' => 'food',
                '--limit' => 2
            ]
        );
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
        $application->add(new ConvertCommand($this->writerService, $this->readerService, $this->fileService, $this->logger, 'ps:convert'));
        $command = $application->find('ps:convert');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                '--source' => 'xml',
                '--target' => 'csv',
                '--source_type' => 'local',
                 'infile' => $path,
                '--key' => 'Merchant',
                '--limit' => 2
            ]
        );
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testXmlToCsvCommandWithWrongFileData()
    {
        $xml = $this->getWrongXMLString();
        $temp = tmpfile();
        fwrite($temp, $xml);
        $path = stream_get_meta_data($temp)['uri'];

        $application = new Application();
        $application->add(new ConvertCommand($this->writerService, $this->readerService, $this->fileService, $this->logger, 'ps:convert'));
        $command = $application->find('ps:convert');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                '--source' => 'xml',
                '--target' => 'csv',
                '--source_type' => 'local',
                'infile' => $path,
                '--key' => 'Merchant',
                '--limit' => 2
            ]
        );
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testXmlToCsvCommandWithNoFileData()
    {
        $this->expectException(FileNotFoundException::class);
        $application = new Application();
        $application->add(new ConvertCommand($this->writerService, $this->readerService, $this->fileService, $this->logger, 'ps:convert'));
        $command = $application->find('ps:convert');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                '--source' => 'xml',
                '--target' => 'csv',
                '--source_type' => 'local',
                'infile' => '/tmp/xyz.xml',
                '--key' => 'Merchant',
                '--limit' => 2
            ]
        );
        $this->assertEquals(1, $commandTester->getStatusCode());
    }
}
