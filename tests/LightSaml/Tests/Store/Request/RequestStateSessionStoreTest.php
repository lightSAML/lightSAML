<?php

namespace LightSaml\Tests\Store\Request;

use LightSaml\Meta\ParameterBag;
use LightSaml\State\Request\RequestState;
use LightSaml\Store\Request\RequestStateSessionStore;
use LightSaml\Tests\BaseTestCase;

class RequestStateSessionStoreTest extends BaseTestCase
{
    public function test_sets_to_session()
    {
        $store = new RequestStateSessionStore(
            $sessionMock = $this->getSessionMock(),
            'main'
        );

        $requestState = new RequestState('aaa');

        $sessionMock->method('set')
            ->with('main_saml_request_state_', $this->isType('array'))
            ->willReturnCallback(function ($name, $value) use ($requestState) {
                $this->assertArrayHasKey('aaa', $value);
                $this->assertSame($requestState, $value['aaa']);
            });

        $store->set($requestState);
    }

    public function test_gets_from_session()
    {
        $store = new RequestStateSessionStore(
            $sessionMock = $this->getSessionMock(),
            'main'
        );

        $id = 'aaa';
        $sessionMock->method('get')
            ->with('main_saml_request_state_')
            ->willReturn([$id => $expected = new RequestState($id)]);

        $actual = $store->get($id);

        $this->assertSame($expected, $actual);
    }

    public function test_remove()
    {
        $store = new RequestStateSessionStore(
            $sessionMock = $this->getSessionMock(),
            'main'
        );

        $id = 'aaa';
        $sessionMock->expects($this->once())
            ->method('get')
            ->willReturn([$id => $expected = new RequestState($id)]);
        $sessionMock->expects($this->once())
            ->method('set')
            ->with('main_saml_request_state_', []);

        $store->remove($id);
    }

    public function test_clear()
    {
        $store = new RequestStateSessionStore(
            $sessionMock = $this->getSessionMock(),
            'main'
        );

        $sessionMock->expects($this->once())
            ->method('set')
            ->with('main_saml_request_state_', []);

        $store->clear();
    }
}
