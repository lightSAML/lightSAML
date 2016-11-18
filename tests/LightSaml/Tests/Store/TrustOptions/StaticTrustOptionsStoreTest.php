<?php

namespace LightSaml\Tests\Store\TrustOptions;

use LightSaml\Meta\TrustOptions\TrustOptions;
use LightSaml\Store\TrustOptions\StaticTrustOptionsStore;

class StaticTrustOptionsStoreTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_be_constructed_without_arguments()
    {
        new StaticTrustOptionsStore();
    }

    public function test_can_add_trust_options()
    {
        $store = new StaticTrustOptionsStore();
        $store->add('id', new TrustOptions());
    }

    public function test_get_returns_null_if_such_id_was_not_added()
    {
        $store = new StaticTrustOptionsStore();
        $result = $store->get('id');
        $this->assertNull($result);
    }

    public function test_get_returns_added_trust_options()
    {
        $id = 'id';
        $trustOptions = new TrustOptions();
        $store = new StaticTrustOptionsStore();
        $store->add($id, $trustOptions);

        $result = $store->get($id);

        $this->assertSame($trustOptions, $result);
    }

    public function test_has_returns_false_if_such_id_was_not_added()
    {
        $store = new StaticTrustOptionsStore();
        $result = $store->has('id');
        $this->assertFalse($result);
    }

    public function test_has_returns_true_when_such_id_was_added()
    {
        $id = 'id';
        $trustOptions = new TrustOptions();
        $store = new StaticTrustOptionsStore();
        $store->add($id, $trustOptions);

        $result = $store->has($id);

        $this->assertTrue($result);
    }
}
