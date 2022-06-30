<?php

declare(strict_types=1);

namespace App;

use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;

/**
 * AppManagerInterface generates App Manager Object used as DI.
 *
 * Primary purpose of AppManagerInterface is to provide access of application variables through
 * injecting App Manager class in client classes.
 */
interface AppManagerInterface
{
    /**
     * Initialize App Manager Service
     *
     * @param LoggerInterface $logger
     * @param string $appName
     * @param string $appVersion
     * @param string $tempDir
     * @param string $dataDir
     */
    public function __construct(LoggerInterface $logger, string $appName, string $appVersion, string $tempDir, string $dataDir);

    /**
     * Gets App name.
     *
     * @return string
     */
    public function getAppName(): string;

    /**
     * Gets app version.
     *
     * @return string
     */
    public function getAppVersion(): string;

    /**
     * Gets temp directory path.
     *
     * @return string
     */
    public function getTempDir(): string;

    /**
     * Gets application logger instance.
     *
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface;
}
