<?php

namespace LightSaml\Tests\Validator\Model\Subject;

use LightSaml\Model\Assertion\NameID;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Assertion\SubjectConfirmation;
use LightSaml\Model\Assertion\SubjectConfirmationData;
use LightSaml\SamlConstants;
use LightSaml\Tests\BaseTestCase;
use LightSaml\Validator\Model\Subject\SubjectValidator;

class SubjectValidatorTest extends BaseTestCase
{
    public function test_fails_when_no_subject_and_no_subject_confirmation()
    {
        $this->expectExceptionMessage("Subject MUST contain either an identifier or a subject confirmation");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $subject = new Subject();

        $nameIdValidatorMock = $this->getNameIdValidatorMock();

        $validator = new SubjectValidator($nameIdValidatorMock);

        $validator->validateSubject($subject);
    }

    public function test_name_id_validator_is_called_when_name_id_present()
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

    public function test_name_id_validator_is_not_called_when_no_name_id()
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

    public function test_name_id_validator_is_called_for_subject_confirmation_name_id()
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

    public function test_fails_on_empty_method()
    {
        $this->expectExceptionMessage("Method attribute of SubjectConfirmation MUST contain at least one non-whitespace character");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $subject = new Subject();

        $subjectConfirmation = new SubjectConfirmation();

        $subject->addSubjectConfirmation($subjectConfirmation);

        $validator = new SubjectValidator($this->getNameIdValidatorMock());

        $validator->validateSubject($subject);
    }

    public function test_fails_on_invalid_method()
    {
        $this->expectExceptionMessage("SubjectConfirmation element has Method attribute which is not a wellformed absolute uri");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $subject = new Subject();

        $subjectConfirmation = new SubjectConfirmation();
        $subjectConfirmation->setMethod('in valid');

        $subject->addSubjectConfirmation($subjectConfirmation);

        $validator = new SubjectValidator($this->getNameIdValidatorMock());

        $validator->validateSubject($subject);
    }

    public function test_fails_on_invalid_recipient()
    {
        $this->expectExceptionMessage("Recipient of SubjectConfirmationData must be a wellformed absolute URI");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
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

    public function test_fails_on_not_on_or_after_less_then_not_before()
    {
        $this->expectExceptionMessage("SubjectConfirmationData NotBefore MUST be less than NotOnOrAfter");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
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
        return $this->getMockBuilder(\LightSaml\Validator\Model\NameId\NameIdValidatorInterface::class)->getMock();
    }
}
