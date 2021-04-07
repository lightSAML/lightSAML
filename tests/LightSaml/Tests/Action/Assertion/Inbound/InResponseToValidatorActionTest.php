<?php

namespace LightSaml\Tests\Action\Assertion\Inbound;

use LightSaml\Action\Assertion\Inbound\InResponseToValidatorAction;
use LightSaml\Context\Profile\ProfileContexts;
use LightSaml\Context\Profile\RequestStateContext;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Assertion\SubjectConfirmation;
use LightSaml\Model\Assertion\SubjectConfirmationData;
use LightSaml\State\Request\RequestState;
use LightSaml\Tests\BaseTestCase;

class InResponseToValidatorActionTest extends BaseTestCase
{
    public function test_constructs_with_logger()
    {
        new InResponseToValidatorAction($this->getLoggerMock(), $this->getRequestStateStoreMock());
        $this->assertTrue(true);
    }

    public function test_does_nothing_if_assertion_has_no_subject()
    {
        $action = new InResponseToValidatorAction(
            $this->getLoggerMock(),
            $this->getRequestStateStoreMock()
        );

        $context = $this->getAssertionContext($assertion = new Assertion());

        $action->execute($context);

        $this->assertTrue(true);
    }

    public function test_throws_context_exception_on_unknown_in_response_to()
    {
        $action = new InResponseToValidatorAction(
            $this->getLoggerMock(),
            $this->getRequestStateStoreMock()
        );

        $context = $this->getAssertionContext($assertion = new Assertion());
        $assertion->setSubject($subject = new Subject());
        $subject->addSubjectConfirmation($subjectConfirmation = new SubjectConfirmation());
        $subjectConfirmation->setSubjectConfirmationData(new SubjectConfirmationData());
        $subjectConfirmation->getSubjectConfirmationData()->setInResponseTo('123123123');

        $this->expectExceptionMessage("Unknown InResponseTo '123123123'");
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);

        $action->execute($context);
    }

    public function test_adds_known_in_response_to_request_state_to_context()
    {
        $action = new InResponseToValidatorAction(
            $this->getLoggerMock(),
            $requestStateMock = $this->getRequestStateStoreMock()
        );

        $context = $this->getAssertionContext($assertion = new Assertion());
        $assertion->setSubject($subject = new Subject());
        $subject->addSubjectConfirmation($subjectConfirmation = new SubjectConfirmation());
        $subjectConfirmation->setSubjectConfirmationData(new SubjectConfirmationData());
        $subjectConfirmation->getSubjectConfirmationData()->setInResponseTo($inResponseTo = '123123123');

        $requestStateMock->expects($this->once())
            ->method('get')
            ->with($inResponseTo)
            ->willReturn(new RequestState($inResponseTo));

        $action->execute($context);

        /** @var RequestStateContext $requestStateContext */
        $requestStateContext = $context->getSubContext(ProfileContexts::REQUEST_STATE);
        $this->assertInstanceOf(RequestStateContext::class, $requestStateContext);
        $this->assertEquals($inResponseTo, $requestStateContext->getRequestState()->getId());
    }
}
