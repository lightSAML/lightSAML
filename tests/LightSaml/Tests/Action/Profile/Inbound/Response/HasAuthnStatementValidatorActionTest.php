<?php

namespace LightSaml\Tests\Action\Profile\Inbound\Response;

use LightSaml\Action\Profile\Inbound\Response\HasAuthnStatementValidatorAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Protocol\Response;
use LightSaml\Profile\Profiles;
use LightSaml\Tests\BaseTestCase;

class HasAuthnStatementValidatorActionTest extends BaseTestCase
{
    public function test_constructs_with_logger()
    {
        new HasAuthnStatementValidatorAction($this->getLoggerMock());
        $this->assertTrue(true);
    }

    public function test_does_nothing_if_there_is_at_least_one_authn_statement()
    {
        $action = new HasAuthnStatementValidatorAction($this->getLoggerMock());

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage($response = new Response());
        $response->addAssertion($assertion = new Assertion());
        $assertion->addItem(new AuthnStatement());

        $action->execute($context);

        $this->assertTrue(true);
    }

    public function test_throws_context_exception_if_no_authn_statement()
    {
        $this->expectExceptionMessage("Response must have at least one Assertion containing AuthnStatement element");
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $action = new HasAuthnStatementValidatorAction($this->getLoggerMock());

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage($response = new Response());

        $action->execute($context);
    }
}
