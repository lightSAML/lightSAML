<?php

namespace LightSaml\Tests\Store\Sso;

use LightSaml\State\Sso\SsoState;
use LightSaml\Store\Sso\SsoStateFixedStore;

class SsoStateFixedStoreTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_be_constructed_without_arguments()
    {
        new SsoStateFixedStore();
    }

    public function test_get_returns_object_created_by_default()
    {
        $store = new SsoStateFixedStore();
        $result = $store->get();
        $this->assertInstanceOf(SsoState::class, $result);
    }

    public function test_can_set_sso_state()
    {
        $store = new SsoStateFixedStore();
        $store->set(new SsoState());
    }

    public function test_get_returns_set_object()
    {
        $store = new SsoStateFixedStore();
        $store->set($state = new SsoState());
        $this->assertSame($state, $store->get());
    }
}
