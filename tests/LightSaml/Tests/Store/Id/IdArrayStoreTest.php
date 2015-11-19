<?php

namespace LightSaml\Tests\Store\Id;

use LightSaml\Store\Id\IdArrayStore;

class IdArrayStoreTest extends \PHPUnit_Framework_TestCase
{
    public function test_works()
    {
        $store = new IdArrayStore();
        $store->set('aaa', '111', new \DateTime('+1 day'));
        $store->set('aaa', '222', new \DateTime('+1 day'));
        $store->set('bbb', '333', new \DateTime('+1 day'));

        $this->assertTrue($store->has('aaa', '111'));
        $this->assertTrue($store->has('aaa', '222'));
        $this->assertTrue($store->has('bbb', '333'));

        $this->assertFalse($store->has('xxx', '888'));
    }
}
