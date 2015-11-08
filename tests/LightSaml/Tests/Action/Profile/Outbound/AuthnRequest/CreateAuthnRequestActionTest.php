<?php

namespace LightSaml\Tests\Action\Profile\Outbound\AuthnRequest;

use LightSaml\Action\Profile\Outbound\AuthnRequest\CreateAuthnRequestAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Profile\Profiles;
use LightSaml\Tests\TestHelper;

class CreateAuthnRequestActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger()
    {
        new CreateAuthnRequestAction(TestHelper::getLoggerMock($this));
    }

    public function test_creates_outbounding_authn_request()
    {
        $action = new CreateAuthnRequestAction(TestHelper::getLoggerMock($this));

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_SP);

        $action->execute($context);

        $this->assertInstanceOf(AuthnRequest::class, $context->getOutboundMessage());
    }
}
