<?php

namespace LightSaml\Tests\Functional\Model\Protocol;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\Model\Protocol\LogoutResponse;
use LightSaml\Model\Protocol\Response;
use LightSaml\Model\Protocol\SamlMessage;

class SamlMessageDeserializationTest extends \PHPUnit_Framework_TestCase
{
    public function deserialize_provider()
    {
        return [
            ['<samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"></samlp:AuthnRequest>', AuthnRequest::class],
            ['<!--comment--><samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"></samlp:AuthnRequest>', AuthnRequest::class],
            ['<samlp:Response xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"></samlp:Response>', Response::class],
            ['<!--comment--><samlp:Response xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"></samlp:Response>', Response::class],
            ['<samlp:LogoutRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"></samlp:LogoutRequest>', LogoutRequest::class],
            ['<!--comment--><samlp:LogoutRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"></samlp:LogoutRequest>', LogoutRequest::class],
            ['<samlp:LogoutResponse xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"></samlp:LogoutResponse>', LogoutResponse::class],
            ['<!--comment--><samlp:LogoutResponse xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"></samlp:LogoutResponse>', LogoutResponse::class],
        ];
    }

    /**
     * @dataProvider deserialize_provider
     */
    public function test_deserialize($xml, $expectedType)
    {
        $deserializationContext = new DeserializationContext();
        $samlMessage = SamlMessage::fromXML($xml, $deserializationContext);
        $this->assertInstanceOf($expectedType, $samlMessage);
    }
}
