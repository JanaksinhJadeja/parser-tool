<?php

namespace App\Tests\Util;

use App\Util\Util;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Util\Util
 */
class UtilTest extends TestCase
{
    public function testMakeArrayFlatReturnsCorrectArray()
    {
        $array = Util::makeArrayFlat(['ab' => ['x', 'y', 'z'], 'cd' => ['1', '2', '3'], 'ef' => []]);
        $this->assertEquals(7, count($array));
    }
}
