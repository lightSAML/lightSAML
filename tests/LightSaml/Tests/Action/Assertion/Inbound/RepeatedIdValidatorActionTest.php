<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Action\Assertion\Inbound;

use LightSaml\Action\Assertion\Inbound\RepeatedIdValidatorAction;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Assertion\SubjectConfirmation;
use LightSaml\Model\Assertion\SubjectConfirmationData;
use LightSaml\SamlConstants;
use LightSaml\Tests\BaseTestCase;

class RepeatedIdValidatorActionTest extends BaseTestCase
{
    public function test_constructs_with_logger_and_id_store()
    {
        new RepeatedIdValidatorAction($this->getLoggerMock(), $this->getIdStoreMock());
        $this->assertTrue(true);
    }

    public function test_does_nothing_if_assertion_has_no_bearer_subject()
    {
        $action = new RepeatedIdValidatorAction($this->getLoggerMock(), $this->getIdStoreMock());

        $assertionContext = $this->getAssertionContext(new Assertion());

        $action->execute($assertionContext);

        $this->assertTrue(true);
    }

    public function test_throws_context_exception_when_bearer_assertion_has_no_id()
    {
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $this->expectExceptionMessage('Bearer Assertion must have ID attribute');

        $action = new RepeatedIdValidatorAction(
            $loggerMock = $this->getLoggerMock(),
            $idStoreMock = $this->getIdStoreMock()
        );

        $assertionContext = $this->getAssertionContext($assertion = new Assertion());
        $assertion->addItem(new AuthnStatement());
        $assertion->setSubject(new Subject());
        $assertion->getSubject()->addSubjectConfirmation($subjectConfirmation = new SubjectConfirmation());
        $subjectConfirmation->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER);

        $loggerMock->expects($this->once())
            ->method('error')
            ->with('Bearer Assertion must have ID attribute', $this->isType('array'));

        $action->execute($assertionContext);
    }

    public function test_throws_context_exception_when_bearer_assertion_has_no_issuer()
    {
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $this->expectExceptionMessage('Bearer Assertion must have Issuer element');

        $action = new RepeatedIdValidatorAction(
            $loggerMock = $this->getLoggerMock(),
            $idStoreMock = $this->getIdStoreMock()
        );

        $assertionContext = $this->getAssertionContext($assertion = new Assertion());
        $assertion->setId($assertionId = '123');
        $assertion->addItem(new AuthnStatement());
        $assertion->setSubject(new Subject());
        $assertion->getSubject()->addSubjectConfirmation($subjectConfirmation = new SubjectConfirmation());
        $subjectConfirmation->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER);

        $loggerMock->expects($this->once())
            ->method('error')
            ->with('Bearer Assertion must have Issuer element', $this->isType('array'));

        $action->execute($assertionContext);
    }

    public function test_throws_context_exception_for_known_assertion_id()
    {
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $this->expectExceptionMessage('Repeated assertion id \'123\' of issuer \'http://issuer.com\'');

        $action = new RepeatedIdValidatorAction(
            $loggerMock = $this->getLoggerMock(),
            $idStoreMock = $this->getIdStoreMock()
        );

        $assertionContext = $this->getAssertionContext($assertion = new Assertion());
        $assertion->setId($assertionId = '123');
        $assertion->setIssuer(new Issuer($issuer = 'http://issuer.com'));
        $assertion->addItem(new AuthnStatement());
        $assertion->setSubject(new Subject());
        $assertion->getSubject()->addSubjectConfirmation($subjectConfirmation = new SubjectConfirmation());
        $subjectConfirmation->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER);

        $idStoreMock->expects($this->once())
            ->method('has')
            ->with($issuer, $assertionId)
            ->willReturn(true);

        $loggerMock->expects($this->once())
            ->method('error')
            ->with("Repeated assertion id '123' of issuer 'http://issuer.com'", $this->isType('array'));

        $action->execute($assertionContext);
    }

    public function test_throws_context_exception_if_no_subject_confirmation_data()
    {
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $this->expectExceptionMessage('Bearer SubjectConfirmation must have SubjectConfirmationData element');

        $action = new RepeatedIdValidatorAction(
            $loggerMock = $this->getLoggerMock(),
            $idStoreMock = $this->getIdStoreMock()
        );

        $assertionContext = $this->getAssertionContext($assertion = new Assertion());
        $assertion->setId($assertionId = '123');
        $assertion->setIssuer(new Issuer($issuer = 'http://issuer.com'));
        $assertion->addItem(new AuthnStatement());
        $assertion->setSubject(new Subject());
        $assertion->getSubject()->addSubjectConfirmation($subjectConfirmation = new SubjectConfirmation());
        $subjectConfirmation->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER);

        $idStoreMock->expects($this->once())
            ->method('has')
            ->with($issuer, $assertionId)
            ->willReturn(false);

        $loggerMock->expects($this->once())
            ->method('error')
            ->with('Bearer SubjectConfirmation must have SubjectConfirmationData element', $this->isType('array'));

        $action->execute($assertionContext);
    }

    public function test_throws_context_exception_if_no_not_on_or_after_attribute()
    {
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $this->expectExceptionMessage('Bearer SubjectConfirmation must have NotOnOrAfter attribute');

        $action = new RepeatedIdValidatorAction(
            $loggerMock = $this->getLoggerMock(),
            $idStoreMock = $this->getIdStoreMock()
        );

        $assertionContext = $this->getAssertionContext($assertion = new Assertion());
        $assertion->setId($assertionId = '123');
        $assertion->setIssuer(new Issuer($issuer = 'http://issuer.com'));
        $assertion->addItem(new AuthnStatement());
        $assertion->setSubject(new Subject());
        $assertion->getSubject()->addSubjectConfirmation($subjectConfirmation = new SubjectConfirmation());
        $subjectConfirmation->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER);
        $subjectConfirmation->setSubjectConfirmationData(new SubjectConfirmationData());

        $idStoreMock->expects($this->once())
            ->method('has')
            ->with($issuer, $assertionId)
            ->willReturn(false);

        $loggerMock->expects($this->once())
            ->method('error')
            ->with('Bearer SubjectConfirmation must have NotOnOrAfter attribute', $this->isType('array'));

        $action->execute($assertionContext);
    }

    public function test_sets_unknown_assertion_id_to_store()
    {
        $action = new RepeatedIdValidatorAction(
            $loggerMock = $this->getLoggerMock(),
            $idStoreMock = $this->getIdStoreMock()
        );

        $assertionContext = $this->getAssertionContext($assertion = new Assertion());
        $assertion->setId($assertionId = '123');
        $assertion->setIssuer(new Issuer($issuer = 'http://issuer.com'));
        $assertion->addItem(new AuthnStatement());
        $assertion->setSubject(new Subject());
        $assertion->getSubject()->addSubjectConfirmation($subjectConfirmation = new SubjectConfirmation());
        $subjectConfirmation->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER);
        $subjectConfirmation->setSubjectConfirmationData(new SubjectConfirmationData());
        $subjectConfirmation->getSubjectConfirmationData()->setNotOnOrAfter(new \DateTime());

        $idStoreMock->expects($this->once())
            ->method('has')
            ->with($issuer, $assertionId)
            ->willReturn(false);

        $idStoreMock->expects($this->once())
            ->method('set')
            ->with($issuer, $assertionId, $this->isInstanceOf(\DateTime::class));

        $action->execute($assertionContext);
    }
}
