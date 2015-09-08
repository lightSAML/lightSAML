<?php

namespace LightSaml\Tests\Functional;

use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Security\KeyHelper;
use LightSaml\Model\Security\X509Certificate;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\Model\XmlDSig\SignatureXmlReader;

class FooTest extends \PHPUnit_Framework_TestCase
{
    public function testMake()
    {
        $certificate = new X509Certificate();
        $certificate->loadFromFile('d:\www\home\lightSAML\lightSAML\web\sp\saml.crt');
        $privateKey = KeyHelper::createPrivateKey('d:\www\home\lightSAML\lightSAML\web\sp\saml.key', null, true);

        $authnRequest = new AuthnRequest();
        $authnRequest->setID('_894da3368874d2dd637983b6812f66c444f100f205');
        $authnRequest->setIssueInstant('2015-09-13T11:47:33Z');
        $authnRequest->setDestination('https://idp.testshib.org/idp/profile/SAML2/POST/SSO');
        $authnRequest->setIssuer(
            (new Issuer())
            ->setValue('https://mt.evo.loc/sp')
            ->setFormat('urn:oasis:names:tc:SAML:2.0:nameid-format:entity')
        );
        $authnRequest->setSignature(
            new SignatureWriter($certificate, $privateKey)
        );

        $serializationContext = new SerializationContext();
        $authnRequest->serialize($serializationContext->getDocument(), $serializationContext);

        $serializationContext->getDocument()->save('d:\auth-request-from-light.xml');


        $xml = file_get_contents('d:\auth-request-from-light.xml');
        $deserializationContext = new DeserializationContext();
        $deserializationContext->getDocument()->loadXML($xml);

        $authnRequest = new AuthnRequest();
        $authnRequest->deserialize($deserializationContext->getDocument()->firstChild, $deserializationContext);

        $signatureReader = $authnRequest->getSignature();
        if ($signatureReader instanceof SignatureXmlReader) {
            $certificate = new X509Certificate();
            $certificate->loadFromFile('d:\www\home\lightSAML\lightSAML\web\sp\saml.crt');
            $key = KeyHelper::createPublicKey($certificate);
            $ok = $signatureReader->validate($key);
            $this->assertTrue($ok);
        } else {
            throw new \LogicException('Expected Signature Xml Reader');
        }
    }
}
