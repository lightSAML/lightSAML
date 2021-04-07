<?php

namespace LightSaml\Tests\Action\Profile\Inbound\Message;

use LightSaml\Action\Profile\Inbound\Message\MessageSignatureValidatorAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Credential\CredentialInterface;
use LightSaml\Credential\Criteria\MetadataCriteria;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\XmlDSig\SignatureStringReader;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\Profile\Profiles;
use LightSaml\Tests\BaseTestCase;
use LightSaml\Validator\Model\Signature\SignatureValidatorInterface;

class MessageSignatureValidatorActionTest extends BaseTestCase
{
    public function test_constructs_with_logger_and_signature_validator()
    {
        new MessageSignatureValidatorAction($this->getLoggerMock(), $this->getSignatureValidatorMock());
        $this->assertTrue(true);
    }

    public function test_does_nothing_when_message_does_not_have_signature()
    {
        $action = new MessageSignatureValidatorAction(
            $logger = $this->getLoggerMock(),
            $signatureValidator = $this->getSignatureValidatorMock()
        );

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage(new AuthnRequest());

        $logger->expects($this->once())
            ->method('debug')
            ->with('Message is not signed', $this->isType('array'))
        ;

        $action->execute($context);
    }

    public function test_throws_if_not_signature_reader()
    {
        $this->expectExceptionMessage("Expected AbstractSignatureReader");
        $this->expectException(\LightSaml\Error\LightSamlModelException::class);
        $action = new MessageSignatureValidatorAction(
            $logger = $this->getLoggerMock(),
            $signatureValidator = $this->getSignatureValidatorMock()
        );

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage($message = new AuthnRequest());

        $message->setSignature(new SignatureWriter());

        $logger->expects($this->once())
            ->method('critical')
            ->with('Expected AbstractSignatureReader', $this->isType('array'))
        ;

        $action->execute($context);
    }

    public function success_on_validator_returns_credential_provider()
    {
        return [
            [ProfileContext::ROLE_IDP, MetadataCriteria::TYPE_SP],
            [ProfileContext::ROLE_SP, MetadataCriteria::TYPE_IDP],
        ];
    }

    /**
     * @dataProvider success_on_validator_returns_credential_provider
     */
    public function test_success_on_validator_returns_credential($ownRole, $metadataType)
    {
        $action = new MessageSignatureValidatorAction(
            $logger = $this->getLoggerMock(),
            $signatureValidator = $this->getSignatureValidatorMock()
        );

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, $ownRole);
        $context->getInboundContext()->setMessage($message = new AuthnRequest());

        $message->setSignature($signature = new SignatureStringReader());
        $message->setIssuer(new Issuer($issuerValue = 'http://localhost/issuer'));

        $credential = $this->getCredentialMock();
        $credential->expects($this->once())
            ->method('getKeyNames')
            ->willReturn(['key A'])
        ;
        $signatureValidator->expects($this->once())
            ->method('validate')
            ->with($signature, $issuerValue, $metadataType)
            ->willReturn($credential)
        ;
        $logger->expects($this->once())
            ->method('debug')
            ->with('Message signature validated with key "key A"', $this->isType('array'))
        ;
        $logger->expects($this->never())
            ->method('warning')
        ;

        $action->execute($context);
    }

    public function test_warning_logged_if_no_verification()
    {
        $action = new MessageSignatureValidatorAction(
            $logger = $this->getLoggerMock(),
            $signatureValidator = $this->getSignatureValidatorMock()
        );

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage($message = new AuthnRequest());

        $message->setSignature($signature = new SignatureStringReader());
        $message->setIssuer(new Issuer($issuerValue = 'http://localhost/issuer'));

        $signatureValidator->expects($this->once())
            ->method('validate')
            ->willReturn(null)
        ;
        $logger->expects($this->never())
            ->method('debug')
        ;
        $logger->expects($this->once())
            ->method('warning')
            ->with('Signature verification was not performed', $this->isType('array'))
        ;

        $action->execute($context);
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
