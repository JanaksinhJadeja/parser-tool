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
 * @covers \App\Command\XmlToSqliteCommand
 * @covers \App\AppManager
 * @covers \App\Converter\Writer\SqliteWriter
 * @covers \App\Converter\Parser\XmlParser
 * @covers \App\Util\Util
 * @covers \App\Converter\Exception\FileNotFoundOnUrlException
 * @covers \App\Converter\Exception\InvalidXmlFormatFoundException
 * @covers \App\Converter\Exception\FileNotFoundException
 */
class XmlToSqliteCommandTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testXmlToSqliteCommandGivesRuntimeExceptionOnNoArguments()
    {
        $this->expectException(RuntimeException::class);
        $application = new Application();
        $application->add(new XmlToSqliteCommand($this->appManager, 'ps:xmltosqlite'));
        $command = $application->find('ps:xmltosqlite');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);
    }


    public function testXmlToSqliteCommandGivesCorrectOutput()
    {
        $application = new Application();
        $application->add(new XmlToSqliteCommand($this->appManager, 'ps:xmltosqlite'));
        $command = $application->find('ps:xmltosqlite');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['infile' => 'https://www.w3schools.com/xml/simple.xml', 'key' => 'food', '--limit' => 2]);
        $commandTester->assertCommandIsSuccessful();
    }

    public function testXmlToSqliteCommandGivesCorrectReturnOnWrongUrlFile()
    {
        $application = new Application();
        $application->add(new XmlToSqliteCommand($this->appManager, 'ps:xmltosqlite'));
        $command = $application->find('ps:xmltosqlite');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['infile' => 'https://www.w3schools.com/xml/simple2.xml', 'key' => 'food', '--limit' => 2]);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testXmlToSqliteCommandWithNoFileData()
    {
        $application = new Application();
        $application->add(new XmlToSqliteCommand($this->appManager, 'ps:xmltocsv'));
        $command = $application->find('ps:xmltocsv');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['infile' => '/tmp/xyz.xml', 'key' => 'Merchant', '--limit' => 2]);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }
}
