<?php

namespace App\Tests;

use App\Command\ConvertCommand;
use App\Kernel;
use App\Services\FileService;
use App\Services\ReaderService;
use App\Services\WriterService;

/**
 * @covers \App\Kernel
 * @covers \App\Command\ConvertCommand
 * @covers \App\AppManager
 */
class KernelTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->writerService  = new WriterService();
        $this->readerService  = new ReaderService();
        $this->fileService    = new FileService();
    }

    public function testKernelInitialize(): void
    {
        $kernel = new Kernel([new ConvertCommand($this->writerService, $this->readerService, $this->fileService, $this->logger, 'ps:convert')]);

        $this->assertInstanceOf(Kernel::class, $kernel);
        $this->assertEquals('UNKNOWN', $kernel->getName());
        $this->assertEquals('UNKNOWN', $kernel->getVersion());
    }
}
