<?php

namespace LightSaml\Tests\Action\Profile\Inbound\Message;

use LightSaml\Action\Profile\Inbound\Message\AssertBindingTypeAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Profile\Profiles;
use LightSaml\SamlConstants;
use LightSaml\Tests\TestHelper;

class AssertBindingTypeActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_construct_with_logger_and_expected_binding_types()
    {
        new AssertBindingTypeAction(
            TestHelper::getLoggerMock($this),
            [SamlConstants::BINDING_SAML2_HTTP_POST]
        );
    }

    public function test_passes_with_inbound_binding_type_being_one_of_expected()
    {
        $action = new AssertBindingTypeAction(
            TestHelper::getLoggerMock($this),
            [SamlConstants::BINDING_SAML2_HTTP_POST]
        );

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setBindingType(SamlConstants::BINDING_SAML2_HTTP_POST);

        $action->execute($context);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Unexpected binding type "urn:oasis:names:tc:SAML:2.0:bindings:SOAP" - expected binding types are: urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST
     */
    public function test_throws_when_inbound_binding_type_not_one_of_expected()
    {
        $action = new AssertBindingTypeAction(
            $logger = TestHelper::getLoggerMock($this),
            [SamlConstants::BINDING_SAML2_HTTP_POST]
        );

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setBindingType(SamlConstants::BINDING_SAML2_SOAP);

        $logger->expects($this->once())
            ->method('critical')
            ->willReturnCallback(function ($message, $arr) {
                $this->assertEquals('Unexpected binding type "urn:oasis:names:tc:SAML:2.0:bindings:SOAP" - expected binding types are: urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST', $message);
                $this->assertTrue(is_array($arr));
            });

        $action->execute($context);
    }
}
