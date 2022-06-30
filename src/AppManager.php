<?php

declare(strict_types=1);

namespace App;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Psr\Log\LoggerInterface;

/**
 * Primary purpose of AppManager is to provide access of application variables through
 * injecting App Manager class in client classes.
 */
final class AppManager implements AppManagerInterface
{
    private string $appName;

    private string $appVersion;

    private string $tempDir;

    private string $dataDir;

    private LoggerInterface $logger;

    /**
     * @inheritDoc
     */
    public function __construct(LoggerInterface $logger, string $appName, string $appVersion, string $tempDir, string $dataDir)
    {
        $this->logger = $logger;
        $this->appName = $appName;
        $this->appVersion = $appVersion;
        $this->tempDir = $tempDir;
        $this->dataDir = $dataDir;
    }

    /**
     * @inheritDoc
     */
    public function getAppName(): string
    {
        return $this->appName;
    }

    /**
     * @inheritDoc
     */
    public function getAppVersion(): string
    {
        return $this->appVersion;
    }

    /**
     * @inheritDoc
     */
    public function getTempDir(): string
    {
        return $this->tempDir;
    }

    /**
     * @inheritDoc
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
