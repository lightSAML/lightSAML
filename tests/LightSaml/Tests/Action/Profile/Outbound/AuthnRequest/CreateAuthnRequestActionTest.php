<?php

namespace LightSaml\Tests\Action\Profile\Outbound\AuthnRequest;

use LightSaml\Action\Profile\Outbound\AuthnRequest\CreateAuthnRequestAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Profile\Profiles;

class CreateAuthnRequestActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger()
    {
        new CreateAuthnRequestAction($this->getLoggerMock());
    }

    public function test_creates_outbounding_authn_request()
    {
        $action = new CreateAuthnRequestAction($this->getLoggerMock());

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_SP);

        $action->execute($context);

        $this->assertInstanceOf(AuthnRequest::class, $context->getOutboundMessage());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface
     */
    private function getLoggerMock()
    {
        return $this->getMock(\Psr\Log\LoggerInterface::class);
    }
}
