<?php

namespace App\Tests;

use App\Service\Cache;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function testCache() {
        Cache::$_mock = true;

        $addValue = "value";
        $addKey = "key";
        $hash = array("key1" => "value1");
        Cache::load()->set($addKey, $addValue, $hash);

        $get = Cache::load()->get($addKey, $hash);
        $this->assertEquals($addValue, $get);

        Cache::load()->delete($addKey, $hash);
        $get = Cache::load()->get($addKey, $hash);
        $this->assertFalse($get);

        Cache::load()->set($addKey, $addValue, $hash, 2);
        $get = Cache::load()->get($addKey, $hash);
        $this->assertEquals($addValue, $get);
        sleep(3);
        $get = Cache::load()->get($addKey, $hash);
        $this->assertFalse($get);

        Cache::$_mock = false;
    }
}
