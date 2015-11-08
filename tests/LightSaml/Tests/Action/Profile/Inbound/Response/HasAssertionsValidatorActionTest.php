<?php

namespace LightSaml\Tests\Action\Profile\Inbound\Response;

use LightSaml\Action\Profile\Inbound\Response\HasAssertionsValidatorAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Protocol\Response;
use LightSaml\Profile\Profiles;
use LightSaml\Tests\TestHelper;

class HasAssertionsValidatorActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger()
    {
        new HasAssertionsValidatorAction(TestHelper::getLoggerMock($this));
    }

    public function test_does_nothing_if_response_has_at_least_one_assertion()
    {
        $action = new HasAssertionsValidatorAction(TestHelper::getLoggerMock($this));

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage($response = new Response());
        $response->addAssertion(new Assertion());

        $action->execute($context);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Response must contain at least one assertion
     */
    public function test_throws_context_exception_if_no_assertions()
    {
        $action = new HasAssertionsValidatorAction(TestHelper::getLoggerMock($this));

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage($response = new Response());

        $action->execute($context);
    }
}
