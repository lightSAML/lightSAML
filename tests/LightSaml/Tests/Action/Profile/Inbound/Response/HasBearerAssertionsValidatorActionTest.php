<?php

namespace LightSaml\Tests\Action\Profile\Inbound\Response;

use LightSaml\Action\Profile\Inbound\Response\HasBearerAssertionsValidatorAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Assertion\SubjectConfirmation;
use LightSaml\Model\Protocol\Response;
use LightSaml\Profile\Profiles;
use LightSaml\SamlConstants;
use LightSaml\Tests\TestHelper;

class HasBearerAssertionsValidatorActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger()
    {
        new HasBearerAssertionsValidatorAction(TestHelper::getLoggerMock($this));
    }

    public function test_does_nothing_if_there_is_bearer_assertion()
    {
        $action = new HasBearerAssertionsValidatorAction(TestHelper::getLoggerMock($this));

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage($response = new Response());
        $response->addAssertion($assertion = new Assertion());
        $assertion->addItem(new AuthnStatement());
        $assertion->setSubject($subject = new Subject());
        $subject->addSubjectConfirmation($subjectConfirmation = new SubjectConfirmation());
        $subjectConfirmation->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER);

        $action->execute($context);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Response must contain at least one bearer assertion
     */
    public function test_throws_context_exception_if_no_bearer_assertion()
    {
        $action = new HasBearerAssertionsValidatorAction(TestHelper::getLoggerMock($this));

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage($response = new Response());

        $action->execute($context);
    }
}
