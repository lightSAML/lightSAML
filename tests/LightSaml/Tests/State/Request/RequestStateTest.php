<?php

namespace LightSaml\Tests\State\Request;

use LightSaml\Meta\ParameterBag;
use LightSaml\State\Request\RequestState;
use LightSaml\Tests\BaseTestCase;

class RequestStateTest extends BaseTestCase
{
    public function test_can_be_constructed_without_arguments()
    {
        new RequestState();
        $this->assertTrue(true);
    }

    public function test_can_be_constructed_wit_id_argument()
    {
        new RequestState('id');
        $this->assertTrue(true);
    }

    /**
     * @deprecated Nonce argument will be removed in 2.0
     */
    public function test_can_be_constructed_wit_id_and_nonce_argument()
    {
        new RequestState('id', 'nonce');
        $this->assertTrue(true);
    }

    public function test_returns_id()
    {
        $state = new RequestState($expectedId = 'id');
        $this->assertEquals($expectedId, $state->getId());
    }

    public function test_has_parameters()
    {
        $state = new RequestState();
        $this->assertInstanceOf(ParameterBag::class, $state->getParameters());
    }
}
