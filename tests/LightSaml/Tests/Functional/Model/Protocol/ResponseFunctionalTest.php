<?php

namespace LightSaml\Tests\Functional\Model\Protocol;

use LightSaml\ClaimTypes;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Protocol\Response;
use LightSaml\Model\XmlDSig\SignatureXmlReader;
use LightSaml\SamlConstants;
use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;

class ResponseFunctionalTest extends \PHPUnit_Framework_TestCase
{
    public function test_deserialize_response01()
    {
        $context = new DeserializationContext();
        $context->getDocument()->load(__DIR__.'/../../../../../../resources/sample/Response/response01.xml');

        $response = new Response();
        $response->deserialize($context->getDocument(), $context);

        $this->assertEquals('_c34b38b9-5da6-4ee8-af49-2af20423d8f5', $response->getID());
        $this->assertEquals('2.0', $response->getVersion());
        $this->assertEquals('2013-10-27T11:55:37Z', $response->getIssueInstantString());
        $this->assertEquals('https://mt.evo.team/simplesaml/module.php/saml/sp/saml2-acs.php/b1', $response->getDestination());
        $this->assertEquals(SamlConstants::CONSENT_UNSPECIFIED, $response->getConsent());
        $this->assertEquals('_513cb532f91881ffdcf054a573826f831cc1603241', $response->getInResponseTo());

        $this->assertNotNull($response->getIssuer());
        $this->assertEquals('https://B1.bead.loc/adfs/services/trust', $response->getIssuer()->getValue());

        $this->assertNotNull($response->getStatus());
        $this->assertEquals(SamlConstants::STATUS_SUCCESS, $response->getStatus()->getStatusCode()->getValue());

        $this->assertCount(1, $response->getAllAssertions());

        $as = $response->getFirstAssertion();
        $this->assertNotNull($as);

        $this->assertEquals('_3ba23925-e43d-4c98-ac99-a05dce99d505', $as->getId());
        $this->assertEquals('2013-10-27T11:55:37Z', $as->getIssueInstantString());
        $this->assertEquals('2.0', $as->getVersion());

        $this->assertNotNull($as->getIssuer());
        $this->assertEquals('https://B1.bead.loc/adfs/services/trust', $as->getIssuer()->getValue());

        $this->assertNotNull($as->getSubject());
        $this->assertEquals(SamlConstants::NAME_ID_FORMAT_TRANSIENT, $as->getSubject()->getNameID()->getFormat());
        $this->assertEquals('bos@bead.loc', $as->getSubject()->getNameID()->getValue());

        $this->assertCount(1, $as->getSubject()->getAllSubjectConfirmations());
        $sc = $as->getSubject()->getFirstSubjectConfirmation();
        $this->assertEquals(SamlConstants::CONFIRMATION_METHOD_BEARER, $sc->getMethod());
        $this->assertEquals('_513cb532f91881ffdcf054a573826f831cc1603241', $sc->getSubjectConfirmationData()->getInResponseTo());
        $this->assertEquals('2013-10-27T12:00:37Z', $sc->getSubjectConfirmationData()->getNotOnOrAfterString());
        $this->assertEquals('https://mt.evo.team/simplesaml/module.php/saml/sp/saml2-acs.php/b1', $sc->getSubjectConfirmationData()->getRecipient());

        $this->assertNotNull($as->getConditions());
        $this->assertEquals('2013-10-27T11:55:37Z', $as->getConditions()->getNotBeforeString());
        $this->assertEquals('2013-10-27T12:55:37Z', $as->getConditions()->getNotOnOrAfterString());

        $this->assertCount(1, $as->getConditions()->getFirstAudienceRestriction()->getAllAudience());
        $this->assertTrue($as->getConditions()->getFirstAudienceRestriction()->hasAudience('https://mt.evo.team/simplesaml/module.php/saml/sp/metadata.php/b1'));

        $this->assertCount(1, $as->getFirstAttributeStatement()->getAllAttributes());
        $attr = $as->getFirstAttributeStatement()->getFirstAttributeByName(ClaimTypes::COMMON_NAME);
        $this->assertNotNull($attr);
        $this->assertEquals(ClaimTypes::COMMON_NAME, $attr->getName());
        $this->assertCount(1, $attr->getAllAttributeValues());
        $this->assertEquals('bos@bead.loc', $attr->getFirstAttributeValue());

        $this->assertEquals('2013-10-27T11:55:36Z', $as->getFirstAuthnStatement()->getAuthnInstantString());
        $this->assertEquals('_3ba23925-e43d-4c98-ac99-a05dce99d505', $as->getFirstAuthnStatement()->getSessionIndex());
        $this->assertEquals(SamlConstants::AUTHN_CONTEXT_WINDOWS, $as->getFirstAuthnStatement()->getAuthnContext()->getAuthnContextClassRef());

        $this->assertNotNull($as->getSignature());
        /** @var SignatureXmlReader $sig */
        $sig = $as->getSignature();
        $this->assertInstanceOf('LightSaml\Model\XmlDSig\SignatureXmlReader', $sig);
        $arrCertificates = $sig->getAllCertificates();
        $this->assertCount(1, $arrCertificates);

        $certificate = (new X509Certificate())->setData($arrCertificates[0]);

        $sig->validate(
            KeyHelper::createPublicKey($certificate)
        );
    }

    public function test_deserialize_invalid02()
    {
        $context = new DeserializationContext();
        $context->getDocument()->load(__DIR__.'/../../../../../../resources/sample/Response/invalid02.xml');

        $response = new Response();
        $response->deserialize($context->getDocument(), $context);

        $this->assertEquals('_274be8a4-c2ba-43ca-a7c6-2f1613762576', $response->getID());
        $this->assertEquals('2.0', $response->getVersion());
        $this->assertEquals('2013-11-17T12:35:10Z', $response->getIssueInstantString());
        $this->assertEquals('_b04e5e6166a0ba08f3ae9327a7145498e9f8a60e2f', $response->getInResponseTo());

        $this->assertNotNull($response->getIssuer());
        $this->assertEquals('https://sts.windows.net/554fadfe-f04f-4975-90cb-ddc8b147aaa2/', $response->getIssuer()->getValue());

        $this->assertNotNull($response->getStatus());
        $this->assertEquals(SamlConstants::STATUS_REQUESTER, $response->getStatus()->getStatusCode()->getValue());
        $this->assertEquals(SamlConstants::STATUS_UNSUPPORTED_BINDING, $response->getStatus()->getStatusCode()->getStatusCode()->getValue());

        $expectedMessage = <<<EOT
ACS75006: An error occurred while processing a SAML2 Authentication request. ACS75003: SAML protocol response cannot be sent via bindings other than HTTP POST. Requested binding: urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect
            Trace ID: d75d5305-d3fc-40b0-9087-d59032682dd9
            Correlation ID: ca26b4bd-23d4-4233-9c28-96bc0a336c39
            Timestamp: 2013-11-17 12:35:10Z
EOT;
        $expectedMessage = trim(str_replace("\r", '', $expectedMessage));
        $this->assertEquals($expectedMessage, trim(str_replace("\r", '', $response->getStatus()->getStatusMessage())));

        $this->assertCount(0, $response->getAllAssertions());
    }
}
