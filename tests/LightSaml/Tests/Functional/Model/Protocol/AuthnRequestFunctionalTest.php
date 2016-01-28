<?php

namespace LightSaml\Tests\Functional\Model\Protocol;

use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\Model\XmlDSig\SignatureXmlReader;
use LightSaml\SamlConstants;

class AuthnRequestFunctionalTest extends \PHPUnit_Framework_TestCase
{
    public function test__deserialize_request01()
    {
        $context = new DeserializationContext();
        $context->getDocument()->load(__DIR__.'/../../../../../../resources/sample/Request/request01.xml');

        $request = new AuthnRequest();
        $request->deserialize($context->getDocument(), $context);

        $this->assertEquals('_8dcc6985f6d9f385f0bbd4562ef848ef3ae78d87d7', $request->getID());
        $this->assertEquals('2.0', $request->getVersion());
        $this->assertEquals('2013-10-10T15:26:20Z', $request->getIssueInstantString());
        $this->assertEquals('https://login.windows.net/554fadfe-f04f-4975-90cb-ddc8b147aaa2/saml2', $request->getDestination());
        $this->assertEquals('https://mt.evo.team/simplesaml/module.php/saml/sp/saml2-acs.php/default-sp', $request->getAssertionConsumerServiceURL());
        $this->assertEquals(SamlConstants::BINDING_SAML2_HTTP_POST, $request->getProtocolBinding());

        $this->assertNotNull($request->getIssuer());
        $this->assertEquals('https://mt.evo.team/simplesaml/module.php/saml/sp/metadata.php/default-sp', $request->getIssuer()->getValue());

        $this->assertNotNull($request->getNameIDPolicy());
        $this->assertEquals(SamlConstants::NAME_ID_FORMAT_PERSISTENT, $request->getNameIDPolicy()->getFormat());
        $this->assertTrue($request->getNameIDPolicy()->getAllowCreate());
    }

    public function test__signed_serialize_deserialize()
    {
        $certificate = new X509Certificate();
        $certificate->loadFromFile(__DIR__.'/../../../../../../web/sp/saml.crt');
        $privateKey = KeyHelper::createPrivateKey(__DIR__.'/../../../../../../web/sp/saml.key', null, true);

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

        $xml = $serializationContext->getDocument()->saveXML();

        // deserialization
        $deserializationContext = new DeserializationContext();
        $deserializationContext->getDocument()->loadXML($xml);

        $authnRequest = new AuthnRequest();
        $authnRequest->deserialize($deserializationContext->getDocument(), $deserializationContext);

        $signatureReader = $authnRequest->getSignature();
        if ($signatureReader instanceof SignatureXmlReader) {
            $certificate = new X509Certificate();
            $certificate->loadFromFile(__DIR__.'/../../../../../../web/sp/saml.crt');
            $key = KeyHelper::createPublicKey($certificate);
            $ok = $signatureReader->validate($key);
            $this->assertTrue($ok);
        } else {
            throw new \LogicException('Expected Signature Xml Reader');
        }

    }
}
