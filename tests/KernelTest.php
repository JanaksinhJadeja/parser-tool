<?php

namespace App\Tests;

use App\AppManager;
use App\Command\XmlToCSVCommand;
use App\Kernel;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Kernel
 * @covers \App\Command\XmlToCSVCommand
 * @covers \App\AppManager
 */
class KernelTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testKernelInitialize(): void
    {
        $kernel = new Kernel([new XmlToCSVCommand($this->appManager, 'ps:xmltocsv')]);

        $this->assertInstanceOf(Kernel::class, $kernel);
        $this->assertEquals('UNKNOWN', $kernel->getName());
        $this->assertEquals('UNKNOWN', $kernel->getVersion());
    }
}
