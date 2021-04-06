<?php

namespace LightSaml\Tests\Functional\Model\Metadata;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Metadata\ContactPerson;
use LightSaml\Model\Metadata\EntitiesDescriptor;
use LightSaml\SamlConstants;
use LightSaml\Tests\BaseTestCase;
use LightSaml\Tests\Helper\ContactPersonChecker;
use LightSaml\Tests\Helper\EndpointChecker;
use LightSaml\Tests\Helper\IndexedEndpointChecker;
use LightSaml\Tests\Helper\KeyDescriptorChecker;
use LightSaml\Tests\Helper\NameIdFormatChecker;
use LightSaml\Tests\Helper\OrganizationChecker;

class EntitiesDescriptorFunctionalTest extends BaseTestCase
{
    public function test__deserialize_test_shib()
    {
        $context = new DeserializationContext();
        $context->getDocument()->load(__DIR__.'/../../../../../../resources/sample/EntitiesDescriptor/testshib-providers.xml');

        $entitiesDescriptor = new EntitiesDescriptor();
        $entitiesDescriptor->deserialize($context->getDocument(), $context);

        $this->assertEquals('urn:mace:shibboleth:testshib:two', $entitiesDescriptor->getName());
        $this->assertCount(2, $entitiesDescriptor->getAllEntityDescriptors());

        //region IDP
        $ed = $entitiesDescriptor->getByEntityId('https://idp.testshib.org/idp/shibboleth');
        $this->assertNotNull($ed);
        $this->assertEquals('https://idp.testshib.org/idp/shibboleth', $ed->getEntityID());
        $this->assertCount(1, $ed->getAllIdpSsoDescriptors());

        $idp = $ed->getFirstIdpSsoDescriptor();
        $this->assertNotNull($idp);
        $this->assertEquals(
            'urn:oasis:names:tc:SAML:1.1:protocol urn:mace:shibboleth:1.0 urn:oasis:names:tc:SAML:2.0:protocol',
            $idp->getProtocolSupportEnumeration()
        );

        $this->assertCount(1, $idp->getAllKeyDescriptors());
        KeyDescriptorChecker::checkCertificateCN($this, null, 'idp.testshib.org', $idp->getFirstKeyDescriptor());

        NameIdFormatChecker::check($this, $idp, array(
            SamlConstants::NAME_ID_FORMAT_TRANSIENT,
            SamlConstants::NAME_ID_FORMAT_SHIB_NAME_ID,
        ));

        $this->assertCount(4, $idp->getAllSingleSignOnServices());
        EndpointChecker::check(
            $this,
            SamlConstants::BINDING_SHIB1_AUTHN_REQUEST,
            'https://idp.testshib.org/idp/profile/Shibboleth/SSO',
            $idp->getFirstSingleSignOnService(SamlConstants::BINDING_SHIB1_AUTHN_REQUEST)
        );
        EndpointChecker::check(
            $this,
            SamlConstants::BINDING_SAML2_HTTP_POST,
            'https://idp.testshib.org/idp/profile/SAML2/POST/SSO',
            $idp->getFirstSingleSignOnService(SamlConstants::BINDING_SAML2_HTTP_POST)
        );
        EndpointChecker::check(
            $this,
            SamlConstants::BINDING_SAML2_HTTP_REDIRECT,
            'https://idp.testshib.org/idp/profile/SAML2/Redirect/SSO',
            $idp->getFirstSingleSignOnService(SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
        );
        EndpointChecker::check(
            $this,
            SamlConstants::BINDING_SAML2_SOAP,
            'https://idp.testshib.org/idp/profile/SAML2/SOAP/ECP',
            $idp->getFirstSingleSignOnService(SamlConstants::BINDING_SAML2_SOAP)
        );

        $this->assertEmpty($idp->getAllSingleLogoutServices());
        $this->assertEmpty($idp->getAllAttributes());
        $this->assertEmpty($idp->getAllOrganizations());
        $this->assertEmpty($idp->getAllContactPersons());

        $this->assertCount(1, $ed->getAllOrganizations());
        OrganizationChecker::check(
            $this,
            'TestShib Two Identity Provider',
            'TestShib Two',
            'http://www.testshib.org/testshib-two/',
            $ed->getFirstOrganization()
        );

        $this->assertCount(1, $ed->getAllContactPersons());
        ContactPersonChecker::check(
            $this,
            ContactPerson::TYPE_TECHNICAL,
            null,
            'Nate',
            'Klingenstein',
            'ndk@internet2.edu',
            null,
            $ed->getFirstContactPerson()
        );
        unset($idp);
        //endregion


        //region SP
        $ed = $entitiesDescriptor->getByEntityId('https://sp.testshib.org/shibboleth-sp');
        $this->assertNotNull($ed);
        $this->assertEquals('https://sp.testshib.org/shibboleth-sp', $ed->getEntityID());
        $this->assertCount(1, $ed->getAllSpSsoDescriptors());

        $sp = $ed->getFirstSpSsoDescriptor();
        $this->assertNotNull($sp);
        $this->assertEquals(
            'urn:oasis:names:tc:SAML:2.0:protocol urn:oasis:names:tc:SAML:1.1:protocol http://schemas.xmlsoap.org/ws/2003/07/secext',
            $sp->getProtocolSupportEnumeration()
        );

        $this->assertCount(1, $sp->getAllKeyDescriptors());
        KeyDescriptorChecker::checkCertificateCN($this, null, 'sp.testshib.org', $sp->getFirstKeyDescriptor());

        $this->assertCount(4, $sp->getAllSingleLogoutServices());
        EndpointChecker::check(
            $this,
            SamlConstants::BINDING_SAML2_SOAP,
            'https://sp.testshib.org/Shibboleth.sso/SLO/SOAP',
            $sp->getFirstSingleLogoutService(SamlConstants::BINDING_SAML2_SOAP)
        );
        EndpointChecker::check(
            $this,
            SamlConstants::BINDING_SAML2_HTTP_REDIRECT,
            'https://sp.testshib.org/Shibboleth.sso/SLO/Redirect',
            $sp->getFirstSingleLogoutService(SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
        );
        EndpointChecker::check(
            $this,
            SamlConstants::BINDING_SAML2_HTTP_POST,
            'https://sp.testshib.org/Shibboleth.sso/SLO/POST',
            $sp->getFirstSingleLogoutService(SamlConstants::BINDING_SAML2_HTTP_POST)
        );
        EndpointChecker::check(
            $this,
            SamlConstants::BINDING_SAML2_HTTP_ARTIFACT,
            'https://sp.testshib.org/Shibboleth.sso/SLO/Artifact',
            $sp->getFirstSingleLogoutService(SamlConstants::BINDING_SAML2_HTTP_ARTIFACT)
        );

        NameIdFormatChecker::check($this, $sp, array(
            SamlConstants::NAME_ID_FORMAT_TRANSIENT,
            SamlConstants::NAME_ID_FORMAT_SHIB_NAME_ID,
        ));

        $this->assertCount(8, $sp->getAllAssertionConsumerServices());
        IndexedEndpointChecker::check(
            $this,
            SamlConstants::BINDING_SAML2_HTTP_POST,
            'https://sp.testshib.org/Shibboleth.sso/SAML2/POST',
            1,
            true,
            $sp->getFirstAssertionConsumerService(SamlConstants::BINDING_SAML2_HTTP_POST)
        );
        IndexedEndpointChecker::check(
            $this,
            SamlConstants::BINDING_SAML2_HTTP_POST_SIMPLE_SIGN,
            'https://sp.testshib.org/Shibboleth.sso/SAML2/POST-SimpleSign',
            2,
            false,
            $sp->getFirstAssertionConsumerService(SamlConstants::BINDING_SAML2_HTTP_POST_SIMPLE_SIGN)
        );
        IndexedEndpointChecker::check(
            $this,
            SamlConstants::BINDING_SAML2_HTTP_ARTIFACT,
            'https://sp.testshib.org/Shibboleth.sso/SAML2/Artifact',
            3,
            false,
            $sp->getFirstAssertionConsumerService(SamlConstants::BINDING_SAML2_HTTP_ARTIFACT)
        );
        IndexedEndpointChecker::check(
            $this,
            SamlConstants::BINDING_SAML1_BROWSER_POST,
            'https://sp.testshib.org/Shibboleth.sso/SAML/POST',
            4,
            false,
            $sp->getFirstAssertionConsumerService(SamlConstants::BINDING_SAML1_BROWSER_POST)
        );
        IndexedEndpointChecker::check(
            $this,
            SamlConstants::BINDING_SAML1_ARTIFACT1,
            'https://sp.testshib.org/Shibboleth.sso/SAML/Artifact',
            5,
            false,
            $sp->getFirstAssertionConsumerService(SamlConstants::BINDING_SAML1_ARTIFACT1)
        );
        IndexedEndpointChecker::check(
            $this,
            SamlConstants::BINDING_WS_FED_WEB_SVC,
            'https://sp.testshib.org/Shibboleth.sso/ADFS',
            6,
            false,
            $sp->getFirstAssertionConsumerService(SamlConstants::BINDING_WS_FED_WEB_SVC)
        );

        $this->assertCount(1, $ed->getAllOrganizations());
        OrganizationChecker::check($this, 'TestShib Two Service Provider', 'TestShib Two', 'http://www.testshib.org/testshib-two/', $ed->getFirstOrganization());

        $this->assertCount(1, $ed->getAllContactPersons());
        ContactPersonChecker::check($this, ContactPerson::TYPE_TECHNICAL, null, 'Nate', 'Klingenstein', 'ndk@internet2.edu', null, $ed->getFirstContactPerson());

        unset($sp);
        //endregion
    }

    public function test_deserialize_ukfederation_metadata()
    {
        $context = new DeserializationContext();
        $context->getDocument()->load(__DIR__.'/../../../../../../resources/sample/EntitiesDescriptor/ukfederation-metadata.xml');

        $entitiesDescriptor = new EntitiesDescriptor();
        $entitiesDescriptor->deserialize($context->getDocument(), $context);
        $this->assertCount(2935, $entitiesDescriptor->getAllEntityDescriptors());
    }

    public function test_throws_on_entity_descriptor()
    {
        $this->expectExceptionMessage("Expected 'EntitiesDescriptor' xml node and 'urn:oasis:names:tc:SAML:2.0:metadata' namespace but got node 'EntityDescriptor' and namespace 'urn:oasis:names:tc:SAML:2.0:metadata'");
        $this->expectException(\LightSaml\Error\LightSamlXmlException::class);
        EntitiesDescriptor::load(__DIR__.'/../../../../../../resources/sample/EntityDescriptor/idp-ed.xml');
    }
}
