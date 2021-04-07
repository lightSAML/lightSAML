<?php

namespace LightSaml\Tests\Functional\Model\Metadata;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\IdpSsoDescriptor;
use LightSaml\Model\Metadata\KeyDescriptor;
use LightSaml\Model\Metadata\SingleLogoutService;
use LightSaml\Model\Metadata\SingleSignOnService;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Model\Metadata\SSODescriptor;
use LightSaml\SamlConstants;
use LightSaml\Tests\BaseTestCase;

class EntityDescriptorFunctionalTest extends BaseTestCase
{
    public function test__deserialization_idp2_ed()
    {
        $context = new DeserializationContext();
        $context->getDocument()->load(__DIR__.'/../../../../../../resources/sample/EntityDescriptor/idp2-ed.xml');

        $ed = new EntityDescriptor();
        $ed->deserialize($context->getDocument(), $context);

        $this->assertEquals('_2240bd9c-30c4-4d2a-ab3e-87a94ea334fd', $ed->getID());
        $this->assertEquals('https://B1.bead.loc/adfs/services/trust', $ed->getEntityID());

        $this->assertNotNull($ed->getSignature());

        $this->assertCount(1, $ed->getAllIdpSsoDescriptors());
        $this->assertCount(1, $ed->getAllSpSsoDescriptors());

        $this->assertCount(1, $ed->getAllContactPersons());

        //region SP
        $sp = $ed->getFirstSpSsoDescriptor();
        $this->assertNotNull($sp);

        $this->assertTrue($sp->getWantAssertionsSigned());
        $this->assertEquals(SamlConstants::PROTOCOL_SAML2, $sp->getProtocolSupportEnumeration());
        $this->assertCount(2, $sp->getAllKeyDescriptors());
        $this->assertCount(1, $sp->getAllKeyDescriptorsByUse(KeyDescriptor::USE_SIGNING));
        $this->assertCount(1, $sp->getAllKeyDescriptorsByUse(KeyDescriptor::USE_ENCRYPTION));

        $this->checkKD($sp, KeyDescriptor::USE_SIGNING, 'MIIC0jCCAbqgAwIBAgIQGFT6omLmWbhAD65bM40rGzANBgkqhkiG9w0BAQsFADAlMSMwIQYDVQQDExpBREZTIFNpZ25pbmcgLSBCMS5iZWFkLmxvYzAeFw0xMzEwMDkxNDUyMDVaFw0xNDEwMDkxNDUyMDVaMCUxIzAhBgNVBAMTGkFERlMgU2lnbmluZyAtIEIxLmJlYWQubG9jMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlGKV64+63lpqdPmCTZ0kt/yKr8xukR1Y071SlmRVV5sSFhTe8cjylPqqxdyEBrfPhpL6vwFQyKfDhuM8T9E+BW5fUdoXO4WmIHrLOxV/BzKv2rDGidlCFzDSQPDxPH2RdQkMBksiauIMSHIYXB92rO4fkcsTgQ6cc+PZp4M3Z/jR1mcxQzz9RQk3I9w2OtI9xcv+uDC5mQU0ZWVHc99VSFQt+zshduwIqxQdHvMdTRslso+oCLEQom42pGCD8TksQTGw4sB7Ctb0mgXdfy0PDIznfi2oDBGtPY2Hkms6/n9xoyCynQea0YYXcpEe7lAvs+t6Lq+ZaKp2kUaa2x8d+QIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQBfwlmaN1iPg0gNiqdVphJjWnzpV4h6/Mz3L0xYzNQeglWCDKCKuajQfmo/AQBErtOWZJsP8avzK79gNRqFHXF6CirjGnL6WO+S6Ug1hvy3xouOxOkIYgZsbmcNL2XO1hIxP4z/QWPthotp3FSUTae2hFBHuy4Gtb+9d9a60GDtgrHnfgVeCTE7CSiaI/D/51JNbtpg2tCpcEzMQgPkQqb8E+V79xc0dnEcI5cBaS6eYgkJgS5gKIMbwaJ/VxzCVGIKwFjFnJedJ5N7zH7OVwor56Q7nuKD7X4yFY9XR3isjGnwXveh9E4d9wD4CMl52AHJpsYsToXsi3eRvApDV/PE');
        $this->checkKD($sp, KeyDescriptor::USE_ENCRYPTION, 'MIIC2DCCAcCgAwIBAgIQGUB7ppqiALBK62Hy+nSDsTANBgkqhkiG9w0BAQsFADAoMSYwJAYDVQQDEx1BREZTIEVuY3J5cHRpb24gLSBCMS5iZWFkLmxvYzAeFw0xMzEwMDkxNDUyMDZaFw0xNDEwMDkxNDUyMDZaMCgxJjAkBgNVBAMTHUFERlMgRW5jcnlwdGlvbiAtIEIxLmJlYWQubG9jMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4ngzZI32ekN1jP8f4N6DpF9jBIwAiLi+bMhhz6lc1QpIbxAsbDQqZEeNSXPj548eEbG2K4dCWFdXWM6fjmjf9olYtDZSJe7XsHciBZoNjxRkLoiNkqIJzLT8Eb+G8NRm8oU6NneziJZtIm6Y6bV/fo3q0fX6aAdOAT7gSoFEEmlkw0Uf49XJCGrS4sMfD5gpXeQpcUuislpnEGgN1NfftE/K1sf6Rdx+5TabCAhEo9wKA9D0qkq6rdf1NEnDSur8RJkrhXy7XIX1pmCw70fLs456vksWzj98j3puZ7soOc3yKf+05UjlNy9ZNnNILgiHarcKMIP4Tp7vp9167TVreQIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQAKBxeIITIO3bu3ln+pH9djN9MTNCFsm7AU9BULbmj/1kHol73Te/xlS4lBzLUjv2WfSm3fN96lzSwYZY/OIQzz69sSnj0sLnU8eSdk5q5l1l5fhMK05cJsUqJQ6eRWsIdxGkDb3c8pUwpSDu3y+sGRAj7aR8tu4JcvqtvLrBpBHk6eCLdj7D3UeRcTW9bSsfxnxhT1pXVaK8VEVrKSOe26vD6VOnGactYff2gL8a3zouqAKFHQKRA8ogci3zoh8Q7ypApuwEohlUlX7boPQ3trwVA24hor/MfCFZ2GPi3at2F4rX+p0ZGrjWzYt0wUIUUQSy15GLPo972qctydzDeE');

        $this->assertCount(2, $sp->getAllSingleLogoutServices());
        $this->assertCount(3, $sp->getAllAssertionConsumerServices());
        $this->assertCount(3, $sp->getAllNameIDFormats());
        $this->assertContainsOnly('LightSaml\Model\Metadata\SingleLogoutService', $sp->getAllSingleLogoutServices());
        $this->assertContainsOnly('LightSaml\Model\Metadata\AssertionConsumerService', $sp->getAllAssertionConsumerServices());
        $this->assertContainsOnly('string', $sp->getAllNameIDFormats());

        $this->checkSLO($sp, SamlConstants::BINDING_SAML2_HTTP_REDIRECT, 'https://b1.bead.loc/adfs/ls/');
        $this->checkSLO($sp, SamlConstants::BINDING_SAML2_HTTP_POST, 'https://b1.bead.loc/adfs/ls/');

        $this->assertTrue($sp->hasNameIDFormat(SamlConstants::NAME_ID_FORMAT_EMAIL));
        $this->assertTrue($sp->hasNameIDFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT));
        $this->assertTrue($sp->hasNameIDFormat(SamlConstants::NAME_ID_FORMAT_TRANSIENT));

        $this->checkACS($sp, SamlConstants::BINDING_SAML2_HTTP_POST, 'https://b1.bead.loc/adfs/ls/', '0', true);
        $this->checkACS($sp, SamlConstants::BINDING_SAML2_HTTP_ARTIFACT, 'https://b1.bead.loc/adfs/ls/', '1', false);
        $this->checkACS($sp, SamlConstants::BINDING_SAML2_HTTP_REDIRECT, 'https://b1.bead.loc/adfs/ls/', '2', false);

        unset($sp);
        //endregion

        //region IDP
        $idp = $ed->getFirstIdpSsoDescriptor();
        $this->assertNotNull($idp);
        $this->assertEquals(SamlConstants::PROTOCOL_SAML2, $idp->getProtocolSupportEnumeration());

        $this->checkKD($idp, KeyDescriptor::USE_ENCRYPTION, 'MIIC2DCCAcCgAwIBAgIQGUB7ppqiALBK62Hy+nSDsTANBgkqhkiG9w0BAQsFADAoMSYwJAYDVQQDEx1BREZTIEVuY3J5cHRpb24gLSBCMS5iZWFkLmxvYzAeFw0xMzEwMDkxNDUyMDZaFw0xNDEwMDkxNDUyMDZaMCgxJjAkBgNVBAMTHUFERlMgRW5jcnlwdGlvbiAtIEIxLmJlYWQubG9jMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4ngzZI32ekN1jP8f4N6DpF9jBIwAiLi+bMhhz6lc1QpIbxAsbDQqZEeNSXPj548eEbG2K4dCWFdXWM6fjmjf9olYtDZSJe7XsHciBZoNjxRkLoiNkqIJzLT8Eb+G8NRm8oU6NneziJZtIm6Y6bV/fo3q0fX6aAdOAT7gSoFEEmlkw0Uf49XJCGrS4sMfD5gpXeQpcUuislpnEGgN1NfftE/K1sf6Rdx+5TabCAhEo9wKA9D0qkq6rdf1NEnDSur8RJkrhXy7XIX1pmCw70fLs456vksWzj98j3puZ7soOc3yKf+05UjlNy9ZNnNILgiHarcKMIP4Tp7vp9167TVreQIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQAKBxeIITIO3bu3ln+pH9djN9MTNCFsm7AU9BULbmj/1kHol73Te/xlS4lBzLUjv2WfSm3fN96lzSwYZY/OIQzz69sSnj0sLnU8eSdk5q5l1l5fhMK05cJsUqJQ6eRWsIdxGkDb3c8pUwpSDu3y+sGRAj7aR8tu4JcvqtvLrBpBHk6eCLdj7D3UeRcTW9bSsfxnxhT1pXVaK8VEVrKSOe26vD6VOnGactYff2gL8a3zouqAKFHQKRA8ogci3zoh8Q7ypApuwEohlUlX7boPQ3trwVA24hor/MfCFZ2GPi3at2F4rX+p0ZGrjWzYt0wUIUUQSy15GLPo972qctydzDeE');
        $this->checkKD($idp, KeyDescriptor::USE_SIGNING, 'MIIC0jCCAbqgAwIBAgIQGFT6omLmWbhAD65bM40rGzANBgkqhkiG9w0BAQsFADAlMSMwIQYDVQQDExpBREZTIFNpZ25pbmcgLSBCMS5iZWFkLmxvYzAeFw0xMzEwMDkxNDUyMDVaFw0xNDEwMDkxNDUyMDVaMCUxIzAhBgNVBAMTGkFERlMgU2lnbmluZyAtIEIxLmJlYWQubG9jMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlGKV64+63lpqdPmCTZ0kt/yKr8xukR1Y071SlmRVV5sSFhTe8cjylPqqxdyEBrfPhpL6vwFQyKfDhuM8T9E+BW5fUdoXO4WmIHrLOxV/BzKv2rDGidlCFzDSQPDxPH2RdQkMBksiauIMSHIYXB92rO4fkcsTgQ6cc+PZp4M3Z/jR1mcxQzz9RQk3I9w2OtI9xcv+uDC5mQU0ZWVHc99VSFQt+zshduwIqxQdHvMdTRslso+oCLEQom42pGCD8TksQTGw4sB7Ctb0mgXdfy0PDIznfi2oDBGtPY2Hkms6/n9xoyCynQea0YYXcpEe7lAvs+t6Lq+ZaKp2kUaa2x8d+QIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQBfwlmaN1iPg0gNiqdVphJjWnzpV4h6/Mz3L0xYzNQeglWCDKCKuajQfmo/AQBErtOWZJsP8avzK79gNRqFHXF6CirjGnL6WO+S6Ug1hvy3xouOxOkIYgZsbmcNL2XO1hIxP4z/QWPthotp3FSUTae2hFBHuy4Gtb+9d9a60GDtgrHnfgVeCTE7CSiaI/D/51JNbtpg2tCpcEzMQgPkQqb8E+V79xc0dnEcI5cBaS6eYgkJgS5gKIMbwaJ/VxzCVGIKwFjFnJedJ5N7zH7OVwor56Q7nuKD7X4yFY9XR3isjGnwXveh9E4d9wD4CMl52AHJpsYsToXsi3eRvApDV/PE');

        $this->assertCount(2, $idp->getAllSingleLogoutServices());
        $this->assertCount(3, $idp->getAllNameIDFormats());
        $this->assertCount(2, $idp->getAllSingleSignOnServices());

        $this->assertContainsOnly('LightSaml\Model\Metadata\SingleLogoutService', $idp->getAllSingleLogoutServices());
        $this->assertContainsOnly('LightSaml\Model\Metadata\SingleSignOnService', $idp->getAllSingleSignOnServices());
        $this->assertContainsOnly('string', $idp->getAllNameIDFormats());

        $this->checkSLO($idp, SamlConstants::BINDING_SAML2_HTTP_REDIRECT, 'https://b1.bead.loc/adfs/ls/');
        $this->checkSLO($idp, SamlConstants::BINDING_SAML2_HTTP_POST, 'https://b1.bead.loc/adfs/ls/');

        $this->assertTrue($idp->hasNameIDFormat(SamlConstants::NAME_ID_FORMAT_EMAIL));
        $this->assertTrue($idp->hasNameIDFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT));
        $this->assertTrue($idp->hasNameIDFormat(SamlConstants::NAME_ID_FORMAT_TRANSIENT));

        $this->checkSSO($idp, SamlConstants::BINDING_SAML2_HTTP_REDIRECT, 'https://b1.bead.loc/adfs/ls/');
        $this->checkSSO($idp, SamlConstants::BINDING_SAML2_HTTP_POST, 'https://b1.bead.loc/adfs/ls/');
        //endregion
    }

    public function test__deserialize_formatted_certificate()
    {
        $context = new DeserializationContext();
        $context->getDocument()->load(__DIR__.'/../../../../../../resources/sample/EntityDescriptor/ed01-formatted-certificate.xml');

        $ed = new EntityDescriptor();
        $ed->deserialize($context->getDocument(), $context);

        $this->assertNotNull($ed->getFirstIdpSsoDescriptor());

        $arr = $ed->getFirstIdpSsoDescriptor()->getAllKeyDescriptors();
        $this->assertCount(1, $arr);
        /** @var KeyDescriptor $kd */
        $kd = array_shift($arr);
        $crt = openssl_x509_parse($kd->getCertificate()->toPem());
        $this->assertEquals('idp.testshib.org', $crt['subject']['CN']);
    }

    public function test_deserialize_engine_surfconext_nl_authentication_idp_metadata()
    {
        $ed = EntityDescriptor::load(__DIR__.'/../../../../../../resources/sample/EntityDescriptor/engine.surfconext.nl_authentication_idp_metadata.xml');
        $this->assertEquals('https://engine.surfconext.nl/authentication/idp/metadata', $ed->getEntityID());
    }

    public function test_throws_on_entities_descriptor_document()
    {
        $this->expectExceptionMessage("Expected 'EntityDescriptor' xml node and 'urn:oasis:names:tc:SAML:2.0:metadata' namespace but got node 'EntitiesDescriptor' and namespace 'urn:oasis:names:tc:SAML:2.0:metadata'");
        $this->expectException(\LightSaml\Error\LightSamlXmlException::class);
        EntityDescriptor::load(__DIR__.'/../../../../../../resources/sample/EntitiesDescriptor/testshib-providers.xml');
    }

    private function checkKD(SSODescriptor $descriptor, $use, $certificate)
    {
        $arrKD = $descriptor->getAllKeyDescriptorsByUse($use);
        /** @var KeyDescriptor $kd */
        $kd = array_shift($arrKD);
        $this->assertNotNull($kd);
        $this->assertEquals($use, $kd->getUse());
        $this->assertNotEmpty($kd->getCertificate()->getData());
        $this->assertEquals($certificate, $kd->getCertificate()->getData());
    }

    private function checkSLO(SSODescriptor $descriptor, $binding, $location)
    {
        $arr = $descriptor->getAllSingleLogoutServicesByBinding($binding);
        /** @var SingleLogoutService $svc */
        $svc = array_shift($arr);
        $this->assertNotNull($svc);
        $this->assertEquals($binding, $svc->getBinding());
        $this->assertEquals($location, $svc->getLocation());
    }

    private function checkACS(SpSsoDescriptor $sp, $binding, $location, $index, $isDefault)
    {
        $arr = $sp->getAllAssertionConsumerServicesByBinding($binding);
        /** @var AssertionConsumerService $svc */
        $svc = array_shift($arr);
        $this->assertNotNull($svc);
        $this->assertEquals($binding, $svc->getBinding());
        $this->assertEquals($location, $svc->getLocation());
        $this->assertEquals($index, $svc->getIndex());
        $this->assertEquals($isDefault, $svc->getIsDefaultBool());
    }

    private function checkSSO(IdpSsoDescriptor $idp, $binding, $location)
    {
        $arr = $idp->getAllSingleSignOnServicesByBinding($binding);
        /** @var SingleSignOnService $svc */
        $svc = array_shift($arr);
        $this->assertNotNull($svc);
        $this->assertEquals($binding, $svc->getBinding());
        $this->assertEquals($location, $svc->getLocation());
    }
}
