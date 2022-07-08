<?php

namespace App\Tests;

use App\AppManager;
use App\Services\FileService;
use App\Services\ReaderService;
use App\Services\WriterService;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class AppTestCase extends TestCase
{
    public string $consolePath;

    public function setUp(): void
    {
        $this->consolePath = dirname(__DIR__).'/bin/console';
        $this->tempDir = __DIR__.'/../data';
        $this->dataDir = __DIR__.'/../data/temp';
        $this->logger = new Logger('app');
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/../var/log/app_test.log'));
        $this->appManager = new AppManager($this->logger, 'data-convert', '0.1.0', $this->dataDir, $this->dataDir);
    }

    public function getWrongXMLString()
    {
        return  <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<Merchants
  <Merchant>
    <merchantID>111</merchantID>
    <subID>22</subID>
  </Merchant>
 <Merchant>
    <merchantID>112</merchantID>
    <subID>23</subID>
  </Merchant>
 <Merchant>
    <merchantID>113</merchantID>
    <subID>24</subID>
    <merchantName>ABC</merchantName>
  </Merchant>
</Merchants>
TEXT;
    }

    public function getCorrectXMLString()
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Merchants>
  <Merchant>
    <merchantID>111</merchantID>
    <subID>22</subID>
  </Merchant>
 <Merchant>
    <merchantID>112</merchantID>
    <subID>23</subID>
  </Merchant>
 <Merchant>
    <merchantID>113</merchantID>
    <subID>24</subID>
    <merchantName>ABC</merchantName>
  </Merchant>
</Merchants>
XML;
    }

    public function getCorrectXMLFile()
    {
        $xml = $this->getCorrectXMLString();
        $temp = tmpfile();
        fwrite($temp, $xml);
        $path = stream_get_meta_data($temp)['uri'];
        return $path;
    }
}
