<?php

namespace LightSaml\Tests\Action\Assertion\Inbound;

use LightSaml\Action\Assertion\Inbound\AssertionSignatureValidatorAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Credential\CredentialInterface;
use LightSaml\Credential\Criteria\MetadataCriteria;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Model\XmlDSig\SignatureStringReader;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\Profile\Profiles;
use LightSaml\Tests\BaseTestCase;
use LightSaml\Validator\Model\Signature\SignatureValidatorInterface;

class AssertionSignatureValidatorActionTest extends BaseTestCase
{
    public function test_constructs_with_logger_and_signature_validator()
    {
        new AssertionSignatureValidatorAction($this->getLoggerMock(), $this->getSignatureValidatorMock());
        $this->assertTrue(true);
    }

    public function test_does_nothing_when_assertion_does_not_have_signature_and_sp_do_not_want_assertions_signed()
    {
        $action = new AssertionSignatureValidatorAction(
            $logger = $this->getLoggerMock(),
            $signatureValidator = $this->getSignatureValidatorMock()
        );

        $assertionContext = $this->getAssertionContext($assertion = new Assertion());
        $assertion->addItem(new AuthnStatement());
        $assertion->setSubject(new Subject());

        $profileContext = $this->getProfileContext(Profiles::SSO_SP_RECEIVE_RESPONSE, ProfileContext::ROLE_SP);
        $profileContext->getOwnEntityContext()->setEntityDescriptor($ownEntityDescriptor = new EntityDescriptor());
        $assertionContext->setParent($profileContext);

        $ownEntityDescriptor->addItem($spSsoDescriptor = new SpSsoDescriptor());
        $spSsoDescriptor->setWantAssertionsSigned(false);

        $logger->expects($this->once())
            ->method('debug')
            ->with('Assertion is not signed', $this->isType('array'))
        ;

        $action->execute($assertionContext);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Assertions must be signed
     */
    public function test_throws_context_exception_when_assertion_does_not_have_signature_and_sp_want_assertions_signed()
    {
        $action = new AssertionSignatureValidatorAction(
            $logger = $this->getLoggerMock(),
            $signatureValidator = $this->getSignatureValidatorMock()
        );

        $assertionContext = $this->getAssertionContext($assertion = new Assertion());
        $assertion->addItem(new AuthnStatement());
        $assertion->setSubject(new Subject());

        $profileContext = $this->getProfileContext(Profiles::SSO_SP_RECEIVE_RESPONSE, ProfileContext::ROLE_SP);
        $profileContext->getOwnEntityContext()->setEntityDescriptor($ownEntityDescriptor = new EntityDescriptor());
        $assertionContext->setParent($profileContext);

        $ownEntityDescriptor->addItem($spSsoDescriptor = new SpSsoDescriptor());
        $spSsoDescriptor->setWantAssertionsSigned(true);

        $logger->expects($this->once())
            ->method('critical')
            ->with('Assertions must be signed', $this->isType('array'))
        ;

        $action->execute($assertionContext);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlModelException
     * @expectedExceptionMessage Expected AbstractSignatureReader
     */
    public function test_throws_model_exception_if_no_signature_reader()
    {
        $action = new AssertionSignatureValidatorAction(
            $logger = $this->getLoggerMock(),
            $signatureValidator = $this->getSignatureValidatorMock()
        );

        $assertionContext = $this->getAssertionContext($assertion = new Assertion());
        $assertion->addItem(new AuthnStatement());
        $assertion->setSubject(new Subject());
        $assertion->setSignature(new SignatureWriter());

        $profileContext = $this->getProfileContext(Profiles::SSO_SP_RECEIVE_RESPONSE, ProfileContext::ROLE_SP);
        $profileContext->getOwnEntityContext()->setEntityDescriptor($ownEntityDescriptor = new EntityDescriptor());
        $assertionContext->setParent($profileContext);

        $ownEntityDescriptor->addItem($spSsoDescriptor = new SpSsoDescriptor());

        $logger->expects($this->once())
            ->method('critical')
            ->with('Expected AbstractSignatureReader', $this->isType('array'))
        ;

        $action->execute($assertionContext);
    }

    public function test_success_on_validator_returns_credential()
    {
        $action = new AssertionSignatureValidatorAction(
            $logger = $this->getLoggerMock(),
            $signatureValidator = $this->getSignatureValidatorMock()
        );

        $assertionContext = $this->getAssertionContext($assertion = new Assertion());
        $assertion->addItem(new AuthnStatement());
        $assertion->setSubject(new Subject());
        $assertion->setSignature($signature = new SignatureStringReader());
        $assertion->setIssuer(new Issuer($issuerValue = 'http://localhost/issuer'));

        $profileContext = $this->getProfileContext(Profiles::SSO_SP_RECEIVE_RESPONSE, ProfileContext::ROLE_SP);
        $profileContext->getOwnEntityContext()->setEntityDescriptor($ownEntityDescriptor = new EntityDescriptor());
        $assertionContext->setParent($profileContext);

        $ownEntityDescriptor->addItem($spSsoDescriptor = new SpSsoDescriptor());

        $credential = $this->getCredentialMock();
        $credential->expects($this->once())
            ->method('getKeyNames')
            ->willReturn(['key A'])
        ;
        $signatureValidator->expects($this->once())
            ->method('validate')
            ->with($signature, $issuerValue, MetadataCriteria::TYPE_IDP)
            ->willReturn($credential)
        ;
        $logger->expects($this->once())
            ->method('debug')
            ->with('Assertion signature validated with key "key A"', $this->isType('array'))
        ;
        $logger->expects($this->never())
            ->method('warning')
        ;

        $action->execute($assertionContext);
    }

    public function test_warning_logged_if_no_verification()
    {
        $action = new AssertionSignatureValidatorAction(
            $logger = $this->getLoggerMock(),
            $signatureValidator = $this->getSignatureValidatorMock()
        );

        $assertionContext = $this->getAssertionContext($assertion = new Assertion());
        $assertion->addItem(new AuthnStatement());
        $assertion->setSubject(new Subject());
        $assertion->setSignature($signature = new SignatureStringReader());
        $assertion->setIssuer(new Issuer($issuerValue = 'http://localhost/issuer'));

        $profileContext = $this->getProfileContext(Profiles::SSO_SP_RECEIVE_RESPONSE, ProfileContext::ROLE_SP);
        $profileContext->getOwnEntityContext()->setEntityDescriptor($ownEntityDescriptor = new EntityDescriptor());
        $assertionContext->setParent($profileContext);

        $ownEntityDescriptor->addItem($spSsoDescriptor = new SpSsoDescriptor());

        $signatureValidator->expects($this->once())
            ->method('validate')
            ->willReturn(null)
        ;
        $logger->expects($this->never())
            ->method('debug')
        ;
        $logger->expects($this->once())
            ->method('warning')
            ->with('Assertion signature verification was not performed', $this->isType('array'))
        ;

        $action->execute($assertionContext);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Validator\Model\Signature\SignatureValidatorInterface
     */
    private function getSignatureValidatorMock()
    {
        return $this->getMockBuilder(SignatureValidatorInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Credential\CredentialInterface
     */
    private function getCredentialMock()
    {
        return $this->getMockBuilder(CredentialInterface::class)->getMock();
    }
}
