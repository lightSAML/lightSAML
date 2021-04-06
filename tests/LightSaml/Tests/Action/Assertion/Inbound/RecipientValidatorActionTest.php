<?php

namespace LightSaml\Tests\Action\Assertion\Inbound;

use LightSaml\Action\Assertion\Inbound\RecipientValidatorAction;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Assertion\SubjectConfirmation;
use LightSaml\Model\Assertion\SubjectConfirmationData;
use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Resolver\Endpoint\Criteria\DescriptorTypeCriteria;
use LightSaml\Resolver\Endpoint\Criteria\LocationCriteria;
use LightSaml\Resolver\Endpoint\Criteria\ServiceTypeCriteria;
use LightSaml\SamlConstants;
use LightSaml\Tests\BaseTestCase;

class RecipientValidatorActionTest extends BaseTestCase
{
    public function test_constructs_with_logger()
    {
        new RecipientValidatorAction($this->getLoggerMock(), $this->getEndpointResolverMock());
        $this->assertTrue(true);
    }

    public function test_does_nothing_when_assertion_has_bearer_subject_but_no_authn_statement()
    {
        $action = new RecipientValidatorAction($loggerMock = $this->getLoggerMock(), $this->getEndpointResolverMock());

        $assertionContext = $this->getAssertionContext($assertion = new Assertion());
        $assertion->setSubject(new Subject());
        $assertion->getSubject()->addSubjectConfirmation((new SubjectConfirmation())->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER));

        $action->execute($assertionContext);

        $this->assertTrue(true);
    }

    public function test_does_nothing_when_assertion_has_authn_statement_but_no_bearer_subject()
    {
        $action = new RecipientValidatorAction($loggerMock = $this->getLoggerMock(), $this->getEndpointResolverMock());

        $assertionContext = $this->getAssertionContext($assertion = new Assertion());
        $assertion->addItem(new AuthnStatement());

        $action->execute($assertionContext);

        $this->assertTrue(true);
    }

    public function test_throws_context_exception_when_bearer_confirmation_has_no_recipient()
    {
        $action = new RecipientValidatorAction($loggerMock = $this->getLoggerMock(), $this->getEndpointResolverMock());

        $assertionContext = $this->getAssertionContext($assertion = new Assertion());
        $assertion->addItem(new AuthnStatement());
        $assertion->setSubject(new Subject());
        $assertion->getSubject()->addSubjectConfirmation($subjectConfirmation = (new SubjectConfirmation())->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER));
        $subjectConfirmation->setSubjectConfirmationData(new SubjectConfirmationData());

        $loggerMock->expects($this->once())
            ->method('error')
            ->with('Bearer SubjectConfirmation must contain Recipient attribute');

        $this->expectExceptionMessage("Bearer SubjectConfirmation must contain Recipient attribute");
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);

        $action->execute($assertionContext);
    }

    public function test_throws_context_exception_when_recipient_does_not_match_any_own_acs_service_location()
    {
        $this->expectExceptionMessage("Recipient 'http://recipient.com' does not match SP descriptor");
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $action = new RecipientValidatorAction(
            $loggerMock = $this->getLoggerMock(),
            $endpointResolver = $this->getEndpointResolverMock()
        );

        $assertionContext = $this->getAssertionContext($assertion = new Assertion());
        $assertion->addItem(new AuthnStatement());
        $assertion->setSubject(new Subject());
        $assertion->getSubject()->addSubjectConfirmation($subjectConfirmation = (new SubjectConfirmation())->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER));
        $subjectConfirmation->setSubjectConfirmationData((new SubjectConfirmationData())->setRecipient($recipient = 'http://recipient.com'));

        $profileContext = $this->getProfileContext();
        $profileContext->getOwnEntityContext()->setEntityDescriptor($ownEntityDescriptor = new EntityDescriptor());
        $assertionContext->setParent($profileContext);

        $endpointResolver->expects($this->once())
            ->method('resolve')
            ->with($this->isInstanceOf(CriteriaSet::class), $this->isType('array'))
            ->willReturnCallback(function (CriteriaSet $criteriaSet) use ($recipient) {
                $this->assertCriteria($criteriaSet, DescriptorTypeCriteria::class, 'getDescriptorType', SpSsoDescriptor::class);
                $this->assertCriteria($criteriaSet, ServiceTypeCriteria::class, 'getServiceType', AssertionConsumerService::class);
                $this->assertCriteria($criteriaSet, LocationCriteria::class, 'getLocation', $recipient);

                return [];
            });

        $loggerMock->expects($this->once())
            ->method('error')
            ->with("Recipient 'http://recipient.com' does not match SP descriptor");

        $action->execute($assertionContext);
    }

    public function test_does_nothing_if_recipient_matches_own_acs_service_location()
    {
        $action = new RecipientValidatorAction(
            $loggerMock = $this->getLoggerMock(),
            $endpointResolver = $this->getEndpointResolverMock()
        );

        $assertionContext = $this->getAssertionContext($assertion = new Assertion());
        $assertion->addItem(new AuthnStatement());
        $assertion->setSubject(new Subject());
        $assertion->getSubject()->addSubjectConfirmation($subjectConfirmation = (new SubjectConfirmation())->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER));
        $subjectConfirmation->setSubjectConfirmationData((new SubjectConfirmationData())->setRecipient($recipient = 'http://recipient.com'));

        $profileContext = $this->getProfileContext();
        $profileContext->getOwnEntityContext()->setEntityDescriptor($ownEntityDescriptor = new EntityDescriptor());
        $assertionContext->setParent($profileContext);

        $endpointResolver->expects($this->once())
            ->method('resolve')
            ->willReturnCallback(function () use ($recipient) {
                return [$this->getEndpointReferenceMock(new AssertionConsumerService())];
            });

        $action->execute($assertionContext);
    }
}
