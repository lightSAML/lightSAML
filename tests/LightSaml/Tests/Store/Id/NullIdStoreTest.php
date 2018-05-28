<?php

namespace LightSaml\Tests\Store\Id;

use LightSaml\Store\Id\NullIdStore;
use LightSaml\Tests\BaseTestCase;

class NullIdStoreTest extends BaseTestCase
{
    public function test_returns_false()
    {
        $store = new NullIdStore();
        $store->set('foo', 'bar', new \DateTime('+1 day'));
        $this->assertFalse($store->has('foo', 'bar'));
    }
}
