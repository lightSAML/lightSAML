<?php

namespace LightSaml\Tests\Model\Protocol;

use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Assertion\Conditions;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\NameIDPolicy;
use LightSaml\SamlConstants;

class AuthnRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testSetSubject()
    {
        $request = new AuthnRequest();
        $request->setSubject($value = new Subject());
        $this->assertSame($value, $request->getSubject());
    }

    public function testSetProviderName()
    {
        $request = new AuthnRequest();
        $request->setProviderName($value = 'some-provider');
        $this->assertSame($value, $request->getProviderName());
    }

    public function testSetIsPassive()
    {
        $request = new AuthnRequest();
        $request->setIsPassive($value = false);
        $this->assertEquals($value, $request->getIsPassive());
        $this->assertEquals('false', $request->getIsPassiveString());
        $request->setIsPassive($value = true);
        $this->assertEquals($value, $request->getIsPassive());
        $this->assertEquals('true', $request->getIsPassiveString());
    }

    public function testSetForceAuthn()
    {
        $request = new AuthnRequest();
        $request->setForceAuthn($value = false);
        $this->assertEquals($value, $request->getForceAuthn());
        $this->assertEquals('false', $request->getForceAuthnString());
        $request->setForceAuthn($value = true);
        $this->assertEquals($value, $request->getForceAuthn());
        $this->assertEquals('true', $request->getForceAuthnString());
    }

    public function testSetConditions()
    {
        $request = new AuthnRequest();
        $request->setConditions($value = new Conditions());
        $this->assertSame($value, $request->getConditions());
    }

    public function testSetAttributeConsumingServiceIndex()
    {
        $request = new AuthnRequest();
        $request->setAttributeConsumingServiceIndex($value = 2);
        $this->assertEquals($value, $request->getAttributeConsumingServiceIndex());
    }

    public function testSetAssertionConsumerServiceIndex()
    {
        $request = new AuthnRequest();
        $request->setAssertionConsumerServiceIndex($value = 2);
        $this->assertEquals($value, $request->getAssertionConsumerServiceIndex());
    }

    public function testSerialize()
    {
        $context = new SerializationContext();
        $request = new AuthnRequest();
        $request->setID('request-id')
            ->setIssueInstant(new \DateTime('2013-10-10T15:26:20Z'))
            ->setDestination('http://destination.com/authn')
            ->setAssertionConsumerServiceURL('http://sp.com/acs')
            ->setProtocolBinding(SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
            ->setIssuer((new Issuer())
                ->setValue('the-issuer'))
            ->setNameIDPolicy((new NameIDPolicy())
                ->setFormat(SamlConstants::NAME_ID_FORMAT_TRANSIENT)
                ->setAllowCreate(true))
        ;

        $request->serialize($context->getDocument(), $context);
        $context->getDocument()->formatOutput = true;
        $xml = $context->getDocument()->saveXML();

        $expectedXml = <<<EOT
<?xml version="1.0"?>
<AuthnRequest xmlns="urn:oasis:names:tc:SAML:2.0:protocol" ID="request-id" Version="2.0" IssueInstant="2013-10-10T15:26:20Z" Destination="http://destination.com/authn" ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" AssertionConsumerServiceURL="http://sp.com/acs">
  <saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">the-issuer</saml:Issuer>
  <NameIDPolicy Format="urn:oasis:names:tc:SAML:2.0:nameid-format:transient" AllowCreate="true"/>
</AuthnRequest>
EOT;

        $xml = trim(str_replace("\r", '', $xml));
        $expectedXml = trim(str_replace("\r", '', $expectedXml));

        $this->assertEquals($expectedXml, $xml);
    }
}
