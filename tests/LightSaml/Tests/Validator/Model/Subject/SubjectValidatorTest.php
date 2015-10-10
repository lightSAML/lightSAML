<?php

namespace LightSaml\Tests\Validator\Model\Subject;

use LightSaml\Model\Assertion\NameID;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Assertion\SubjectConfirmation;
use LightSaml\Model\Assertion\SubjectConfirmationData;
use LightSaml\SamlConstants;
use LightSaml\Validator\Model\Subject\SubjectValidator;

class SubjectValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Subject MUST contain either an identifier or a subject confirmation
     */
    public function testFailsWhenNoSubjectAndNoSubjectConfirmation()
    {
        $subject = new Subject();

        $nameIdValidatorMock = $this->getNameIdValidatorMock();

        $validator = new SubjectValidator($nameIdValidatorMock);

        $validator->validateSubject($subject);
    }

    public function testNameIdValidatorIsCalledWhenNameIdPresent()
    {
        $subject = new Subject();

        $nameId = new NameID();
        $subject->setNameID($nameId);

        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $nameIdValidatorMock->expects($this->once())
            ->method('validateNameId')
            ->with($nameId);

        $validator = new SubjectValidator($nameIdValidatorMock);

        $validator->validateSubject($subject);
    }

    public function testNameIdValidatorIsNotCalledWhenNoNameId()
    {
        $subject = new Subject();

        $subjectConfirmation = new SubjectConfirmation();
        $subjectConfirmation->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER);

        $subject->addSubjectConfirmation($subjectConfirmation);

        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $nameIdValidatorMock->expects($this->never())
            ->method('validateNameId');

        $validator = new SubjectValidator($nameIdValidatorMock);

        $validator->validateSubject($subject);
    }

    public function testNameIdValidatorIsCalledForSubjectConfirmationNameId()
    {
        $subject = new Subject();

        $nameId = new NameID();

        $subjectConfirmation = new SubjectConfirmation();
        $subjectConfirmation->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER);
        $subjectConfirmation->setNameID($nameId);

        $subject->addSubjectConfirmation($subjectConfirmation);

        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $nameIdValidatorMock->expects($this->once())
            ->method('validateNameId')
            ->with($nameId);

        $validator = new SubjectValidator($nameIdValidatorMock);

        $validator->validateSubject($subject);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Method attribute of SubjectConfirmation MUST contain at least one non-whitespace character
     */
    public function testFailsOnEmptyMethod()
    {
        $subject = new Subject();

        $subjectConfirmation = new SubjectConfirmation();

        $subject->addSubjectConfirmation($subjectConfirmation);

        $validator = new SubjectValidator($this->getNameIdValidatorMock());

        $validator->validateSubject($subject);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage SubjectConfirmation element has Method attribute which is not a wellformed absolute uri
     */
    public function testFailsOnInvalidMethod()
    {
        $subject = new Subject();

        $subjectConfirmation = new SubjectConfirmation();
        $subjectConfirmation->setMethod('in valid');

        $subject->addSubjectConfirmation($subjectConfirmation);

        $validator = new SubjectValidator($this->getNameIdValidatorMock());

        $validator->validateSubject($subject);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Recipient of SubjectConfirmationData must be a wellformed absolute URI
     */
    public function testFailsOnInvalidRecipient()
    {
        $subject = new Subject();

        $subjectConfirmationData = new SubjectConfirmationData();
        $subjectConfirmationData->setRecipient('in valid');

        $subjectConfirmation = new SubjectConfirmation();
        $subjectConfirmation->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER);
        $subjectConfirmation->setSubjectConfirmationData($subjectConfirmationData);

        $subject->addSubjectConfirmation($subjectConfirmation);

        $validator = new SubjectValidator($this->getNameIdValidatorMock());

        $validator->validateSubject($subject);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage SubjectConfirmationData NotBefore MUST be less than NotOnOrAfter
     */
    public function testFailsOnNotOnOrAfterLessThenNotBefore()
    {
        $subject = new Subject();

        $subjectConfirmationData = new SubjectConfirmationData();
        $subjectConfirmationData->setNotOnOrAfter(999)
            ->setNotBefore(1000);

        $subjectConfirmation = new SubjectConfirmation();
        $subjectConfirmation->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER);
        $subjectConfirmation->setSubjectConfirmationData($subjectConfirmationData);

        $subject->addSubjectConfirmation($subjectConfirmation);

        $validator = new SubjectValidator($this->getNameIdValidatorMock());

        $validator->validateSubject($subject);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Validator\Model\NameId\NameIdValidatorInterface
     */
    public function getNameIdValidatorMock()
    {
        return $this->getMock('LightSaml\Validator\Model\NameId\NameIdValidatorInterface');
    }
}
