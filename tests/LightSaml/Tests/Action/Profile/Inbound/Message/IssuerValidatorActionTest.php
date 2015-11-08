<?php

namespace LightSaml\Tests\Action\Profile\Inbound\Message;

use LightSaml\Action\Profile\Inbound\Message\IssuerValidatorAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlValidationException;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Profile\Profiles;
use LightSaml\SamlConstants;
use LightSaml\Tests\TestHelper;
use LightSaml\Validator\Model\NameId\NameIdValidatorInterface;

class IssuerValidatorActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger_name_id_validator_and_string()
    {
        new IssuerValidatorAction(TestHelper::getLoggerMock($this), $this->getNameIdValidatorMock(), '');
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Inbound message must have Issuer element
     */
    public function test_throws_if_inbound_message_has_no_issuer()
    {
        $action = new IssuerValidatorAction(TestHelper::getLoggerMock($this), $this->getNameIdValidatorMock(), '');

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage(new AuthnRequest());

        $action->execute($context);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Response Issuer Format if set must have value 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress' but it was 'non-allowed'
     */
    public function test_throws_if_inbound_message_issuer_format_different_then_allowed()
    {
        $action = new IssuerValidatorAction(TestHelper::getLoggerMock($this), $this->getNameIdValidatorMock(), SamlConstants::NAME_ID_FORMAT_EMAIL);

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage(new AuthnRequest());

        $expectedIssuer = new Issuer('http://localhost', 'non-allowed');

        $context->getInboundMessage()->setIssuer($expectedIssuer);

        $action->execute($context);
    }

    public function test_calls_name_id_validator()
    {
        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $action = new IssuerValidatorAction(TestHelper::getLoggerMock($this), $nameIdValidatorMock, $allowedFormat = SamlConstants::NAME_ID_FORMAT_EMAIL);

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage(new AuthnRequest());

        $expectedIssuer = new Issuer('http://localhost', $allowedFormat);

        $context->getInboundMessage()->setIssuer($expectedIssuer);

        $nameIdValidatorMock->expects($this->once())
            ->method('validateNameId')
            ->with($expectedIssuer);

        $action->execute($context);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Error from name id validator
     */
    public function test_wrapps_validation_exception_in_context_exception()
    {
        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $action = new IssuerValidatorAction(TestHelper::getLoggerMock($this), $nameIdValidatorMock, $allowedFormat = SamlConstants::NAME_ID_FORMAT_EMAIL);

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage(new AuthnRequest());

        $expectedIssuer = new Issuer('http://localhost', $allowedFormat);

        $context->getInboundMessage()->setIssuer($expectedIssuer);

        $nameIdValidatorMock->expects($this->once())
            ->method('validateNameId')
            ->with($expectedIssuer)
            ->willThrowException(new LightSamlValidationException('Error from name id validator'))
        ;

        $action->execute($context);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Validator\Model\NameId\NameIdValidatorInterface
     */
    public function getNameIdValidatorMock()
    {
        return $this->getMock(NameIdValidatorInterface::class);
    }
}
