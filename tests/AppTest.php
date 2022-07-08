<?php

namespace App\Tests;

use App\AppManager;
use League\Flysystem\Filesystem;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\AppManager
 */
class AppTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testAppConsoleIsWorking()
    {
        $output = shell_exec($this->consolePath);
        $this->assertStringContainsString('Usage:', $output);
        $this->assertStringContainsString('ps:convert', $output);
    }

    public function testAppManagerReturnsCorrectAppName()
    {
        $this->assertEquals('data-convert', $this->appManager->getAppName());
    }

    public function testAppManagerReturnsCorrectAppVersion()
    {
        $this->assertEquals('0.1.0', $this->appManager->getAppVersion());
    }

    public function testAppManagerReturnsCorrectTempDirectory()
    {
        $this->assertEquals('/var/www/tests/../data/temp', $this->appManager->getTempDir());
    }

    public function testAppManagerReturnsCorrectLogger()
    {
        $this->assertInstanceOf(LoggerInterface::class, $this->appManager->getLogger());
    }
}
