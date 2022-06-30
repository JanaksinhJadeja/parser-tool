<?php

namespace App\Tests;

use App\AppManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class AppTestCase extends TestCase
{
    public string $consolePath;

    public function setUp(): void
    {
        $this->consolePath = dirname(__DIR__).'/bin/console';
        $logger = new Logger('app');
        $logger->pushHandler(new StreamHandler(__DIR__.'/../var/log/app_test.log'));
        $this->assertEquals('Monolog\Logger', get_class($logger));
        $tempDir = __DIR__.'/../data';
        $dataDir = __DIR__.'/../data/temp';
        $this->appManager = new AppManager($logger, 'data-convert', '0.1.0', $dataDir, $tempDir);
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
}
