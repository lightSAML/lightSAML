<?php

namespace LightSaml\Tests\Model\Xsd;

use LightSaml\ClaimTypes;
use LightSaml\Credential\UsageType;
use LightSaml\Helper;
use LightSaml\Model\Assertion\Attribute;
use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Model\Metadata\ContactPerson;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\IdpSsoDescriptor;
use LightSaml\Model\Metadata\KeyDescriptor;
use LightSaml\Model\Metadata\Organization;
use LightSaml\Model\Metadata\SingleLogoutService;
use LightSaml\Model\Metadata\SingleSignOnService;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\SamlConstants;

class EntityDescriptorXsdTest extends AbstractXsdValidationTest
{
    public function test_entity_descriptor_with_xsd()
    {
        $entityDescriptor = new EntityDescriptor();
        $entityDescriptor
            ->setID(Helper::generateID())
            ->setEntityID('https://idp.com')
        ;

        $entityDescriptor->addItem($idpSsoDescriptor = new IdpSsoDescriptor());
        $idpSsoDescriptor
            ->addAttribute((new Attribute(ClaimTypes::EMAIL_ADDRESS))
                ->setNameFormat('urn:oasis:names:tc:SAML:2.0:attrname-format:uri')
                ->setFriendlyName('Email address')
            )
            ->addSingleSignOnService(new SingleSignOnService('https://idp.com/login', SamlConstants::BINDING_SAML2_HTTP_POST))
            ->addSingleSignOnService(new SingleSignOnService('https://idp.com/login', SamlConstants::BINDING_SAML2_HTTP_REDIRECT))
            ->addSingleLogoutService(new SingleLogoutService('https://idp.com/logout', SamlConstants::BINDING_SAML2_HTTP_POST))
            ->addSingleLogoutService(new SingleLogoutService('https://idp.com/logout', SamlConstants::BINDING_SAML2_HTTP_REDIRECT))
            ->addNameIDFormat(SamlConstants::NAME_ID_FORMAT_TRANSIENT)
            ->addNameIDFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT)
            ->addNameIDFormat(SamlConstants::NAME_ID_FORMAT_EMAIL)
            ->setProtocolSupportEnumeration(SamlConstants::PROTOCOL_SAML2)
            ->addKeyDescriptor((new KeyDescriptor(UsageType::SIGNING, $this->getX509Certificate())))
            ->addKeyDescriptor(new KeyDescriptor(UsageType::ENCRYPTION, $this->getX509Certificate()))
        ;

        $entityDescriptor->addItem($spSsoDescriptor = new SpSsoDescriptor());
        $spSsoDescriptor
            ->addAssertionConsumerService(new AssertionConsumerService('https://sp.com/acs', SamlConstants::BINDING_SAML2_HTTP_POST))
            ->addSingleLogoutService(new SingleLogoutService('https://sp.com/logout', SamlConstants::BINDING_SAML2_HTTP_POST))
            ->addSingleLogoutService(new SingleLogoutService('https://sp.com/logout', SamlConstants::BINDING_SAML2_HTTP_REDIRECT))
            ->addNameIDFormat(SamlConstants::NAME_ID_FORMAT_TRANSIENT)
            ->addNameIDFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT)
            ->addNameIDFormat(SamlConstants::NAME_ID_FORMAT_EMAIL)
            ->setProtocolSupportEnumeration(SamlConstants::PROTOCOL_SAML2)
            ->addKeyDescriptor((new KeyDescriptor(UsageType::SIGNING, $this->getX509Certificate())))
            ->addKeyDescriptor(new KeyDescriptor(UsageType::ENCRYPTION, $this->getX509Certificate()))
        ;

        $entityDescriptor
            ->addContactPerson((new ContactPerson())
                ->setContactType(ContactPerson::TYPE_SUPPORT)
                ->setEmailAddress('support@idp.com')
            )
            ->addOrganization((new Organization())
                ->setOrganizationName('Org name')
                ->setOrganizationDisplayName('Org display name')
                ->setOrganizationURL('https://idp.com')
            )
        ;

        $this->sign($entityDescriptor);

        $this->validateMetadata($entityDescriptor);
    }
}
