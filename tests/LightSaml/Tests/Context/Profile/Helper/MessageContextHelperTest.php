<?php

namespace LightSaml\Tests\Context\Profile\Helper;

use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Error\LightSamlContextException;
use LightSaml\Model\Protocol\AbstractRequest;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\Model\Protocol\LogoutResponse;
use LightSaml\Model\Protocol\Response;
use LightSaml\Model\Protocol\SamlMessage;
use LightSaml\Model\Protocol\StatusResponse;

class MessageContextHelperTest extends \PHPUnit_Framework_TestCase
{
    public function helperProvider()
    {
        return [
            ['asSamlMessage', null, LightSamlContextException::class, 'Missing SamlMessage'],
            ['asSamlMessage', $this->getMockForAbstractClass(SamlMessage::class), null, null],

            ['asAuthnRequest', null, LightSamlContextException::class, 'Expected AuthnRequest'],
            ['asAuthnRequest', $this->getMockForAbstractClass(SamlMessage::class), LightSamlContextException::class, 'Expected AuthnRequest'],
            ['asAuthnRequest', new Response(), LightSamlContextException::class, 'Expected AuthnRequest'],
            ['asAuthnRequest', new AuthnRequest(), null, null],

            ['asAbstractRequest', null, LightSamlContextException::class, 'Expected AbstractRequest'],
            ['asAbstractRequest', new Response(), LightSamlContextException::class, 'Expected AbstractRequest'],
            ['asAbstractRequest', $this->getMockForAbstractClass(AbstractRequest::class), null, null],
            ['asAbstractRequest', new AuthnRequest(), null, null],
            ['asAbstractRequest', new LogoutRequest(), null, null],

            ['asResponse', null, LightSamlContextException::class, 'Expected Response'],
            ['asResponse', new AuthnRequest(), LightSamlContextException::class, 'Expected Response'],
            ['asResponse', new LogoutResponse(), LightSamlContextException::class, 'Expected Response'],
            ['asResponse', new Response(), null, null],

            ['asStatusResponse', null, LightSamlContextException::class, 'Expected StatusResponse'],
            ['asStatusResponse', new AuthnRequest(), LightSamlContextException::class, 'Expected StatusResponse'],
            ['asStatusResponse', new Response(), null, null],
            ['asStatusResponse', new LogoutResponse(), null, null],
            ['asStatusResponse', $this->getMockForAbstractClass(StatusResponse::class), null, null],

            ['asLogoutRequest', null, LightSamlContextException::class, 'Expected LogoutRequest'],
            ['asLogoutRequest', new AuthnRequest(), LightSamlContextException::class, 'Expected LogoutRequest'],
            ['asLogoutRequest', new LogoutRequest(), null, null],

            ['asLogoutResponse', null, LightSamlContextException::class, 'Expected LogoutResponse'],
            ['asLogoutResponse', new AuthnRequest(), LightSamlContextException::class, 'Expected LogoutResponse'],
            ['asLogoutResponse', new LogoutRequest(), LightSamlContextException::class, 'Expected LogoutResponse'],
            ['asLogoutResponse', new LogoutResponse(), null, null],
        ];
    }

    /**
     * @dataProvider helperProvider
     */
    public function test__helper($method, SamlMessage $message = null, $expectedException = null, $expectedMessage = null)
    {
        $context = new MessageContext();
        if ($message) {
            $context->setMessage($message);
        }

        if ($expectedException) {
            try {
                call_user_func(['LightSaml\Context\Profile\Helper\MessageContextHelper', $method], $context);
            } catch (\Exception $ex) {
                $this->assertInstanceOf($expectedException, $ex);
                if ($expectedMessage) {
                    $this->assertEquals($expectedMessage, $ex->getMessage());
                }
            }
        } else {
            $actualMessage = call_user_func(['LightSaml\Context\Profile\Helper\MessageContextHelper', $method], $context);
            $this->assertSame($message, $actualMessage);
        }
    }

    public function test__as_saml_message_returns_message()
    {
        $context = new MessageContext();
        $context->setMessage($expectedMessage = $this->getMessageMock());

        $this->assertSame($expectedMessage, MessageContextHelper::asSamlMessage($context));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SamlMessage
     */
    private function getMessageMock()
    {
        return $this->getMockForAbstractClass(SamlMessage::class);
    }
}
