<?php

namespace LightSaml\Tests\State\Request;

use LightSaml\Meta\ParameterBag;
use LightSaml\State\Request\RequestState;

class RequestStateTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_be_constructed_without_arguments()
    {
        new RequestState();
    }

    public function test_can_be_constructed_wit_id_argument()
    {
        new RequestState('id');
    }

    /**
     * @deprecated Nonce argument will be removed in 2.0
     */
    public function test_can_be_constructed_wit_id_and_nonce_argument()
    {
        new RequestState('id', 'nonce');
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
