<?php

namespace LightSaml\Tests\Store\Id;

use LightSaml\Store\Id\NullIdStore;

class NullIdStoreTest extends \PHPUnit_Framework_TestCase
{
    public function test_returns_false()
    {
        $store = new NullIdStore();
        $store->set('foo', 'bar', new \DateTime('+1 day'));
        $this->assertFalse($store->has('foo', 'bar'));
    }
}
