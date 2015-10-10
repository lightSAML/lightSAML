<?php

namespace LightSaml\Tests\Model\Protocol;

use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Assertion\NameID;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\SamlConstants;

class LogoutRequestTest extends \PHPUnit_Framework_TestCase
{
    public function test__serialize()
    {
        $context = new SerializationContext();
        $request = new LogoutRequest();
        $request->setID('request-id')
            ->setIssueInstant(new \DateTime('2013-12-08T17:08:35Z'))
            ->setDestination('http://idp.com/saml/logout')
            ->setNotOnOrAfter(new \DateTime('2013-12-08T17:10:00Z'))
            ->setIssuer((new Issuer())
                ->setValue('the-issuer'))
            ->setNameID((new NameID())
                ->setValue('name-id')
                ->setFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT))
            ->setSessionIndex('123123123')
        ;

        $request->serialize($context->getDocument(), $context);
        $context->getDocument()->formatOutput = true;

        $xml = $context->getDocument()->saveXML();

        $expectedXml = <<<EOT
<?xml version="1.0"?>
<LogoutRequest xmlns="urn:oasis:names:tc:SAML:2.0:protocol" ID="request-id" Version="2.0" IssueInstant="2013-12-08T17:08:35Z" Destination="http://idp.com/saml/logout" NotOnOrAfter="2013-12-08T17:10:00Z">
  <saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">the-issuer</saml:Issuer>
  <saml:NameID xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" Format="urn:oasis:names:tc:SAML:2.0:nameid-format:persistent">name-id</saml:NameID>
  <SessionIndex>123123123</SessionIndex>
</LogoutRequest>
EOT;

        $xml = trim(str_replace("\r", '', $xml));
        $expectedXml = trim(str_replace("\r", '', $expectedXml));

        $this->assertEquals($expectedXml, $xml);
    }
}
