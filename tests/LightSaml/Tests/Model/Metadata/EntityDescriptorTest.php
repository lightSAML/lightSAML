<?php

namespace LightSaml\Tests\Model\Metadata;

use LightSaml\ClaimTypes;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
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
use LightSaml\Credential\X509Certificate;

class EntityDescriptorTest extends \PHPUnit_Framework_TestCase
{
    public function test_serialization()
    {
        $ed = new EntityDescriptor();
        $ed
            ->setEntityID($entityID = 'http://vendor.com/id')
            ->setID($edID = '_127800fe-39ac-46ad-b073-6fb6106797a0')
            ->addItem((new IdpSsoDescriptor())
                ->setWantAuthnRequestsSigned(false)
                ->addSingleSignOnService((new SingleSignOnService())
                    ->setBinding(SamlConstants::BINDING_SAML2_HTTP_POST)
                    ->setLocation('http://idp.example.com/sso/post'))
                ->addSingleSignOnService((new SingleSignOnService())
                    ->setBinding(SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
                    ->setLocation('http://idp.example.com/slo/get'))
                ->addSingleLogoutService((new SingleLogoutService())
                    ->setBinding(SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
                    ->setLocation('http://idp.example.com/slo/redirect'))
                ->addAttribute((new Attribute())
                    ->setName(ClaimTypes::COMMON_NAME)
                    ->setFriendlyName('Common Name')
                    ->addAttributeValue('common name value'))
                ->addNameIDFormat(SamlConstants::NAME_ID_FORMAT_EMAIL)
                ->addNameIDFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT)
                ->addKeyDescriptor((new KeyDescriptor())
                    ->setCertificate((new X509Certificate())
                        ->loadFromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml.crt')))
                ->addOrganization((new Organization())
                    ->setOrganizationName('Organization Name')
                    ->setOrganizationDisplayName('Display Name')
                    ->setOrganizationURL('http://organization.org'))
                ->addContactPerson((new ContactPerson())
                    ->setContactType(ContactPerson::TYPE_SUPPORT)
                    ->setGivenName('Support')
                    ->setSurName('Smith')
                    ->setEmailAddress('support@idp.com')))
            ->addItem((new SpSsoDescriptor())
                ->setAuthnRequestsSigned(false)
                ->setWantAssertionsSigned(false)
                ->addSingleLogoutService((new SingleLogoutService())
                    ->setBinding(SamlConstants::BINDING_SAML2_HTTP_POST)
                    ->setLocation('http://sp.example.com/slo/post'))
                ->addAssertionConsumerService((new AssertionConsumerService())
                    ->setBinding(SamlConstants::BINDING_SAML2_HTTP_POST)
                    ->setLocation('http://sp.example.com/acs/post')
                    ->setIndex(0)
                    ->setIsDefault(true))
                ->addAssertionConsumerService((new AssertionConsumerService())
                    ->setBinding(SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
                    ->setLocation('http://sp.example.com/acs/redirect')
                    ->setIndex(1)
                    ->setIsDefault(false))
                ->addNameIDFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT)
                ->addNameIDFormat(SamlConstants::NAME_ID_FORMAT_TRANSIENT))
        ;

        $context = new SerializationContext();
        $ed->serialize($context->getDocument(), $context);

        $context->getDocument()->formatOutput = true;
        $xml = $context->getDocument()->saveXML();

        $expectedXml = <<<EOT
<?xml version="1.0"?>
<EntityDescriptor xmlns="urn:oasis:names:tc:SAML:2.0:metadata" entityID="http://vendor.com/id" ID="_127800fe-39ac-46ad-b073-6fb6106797a0">
  <IDPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol" WantAuthnRequestsSigned="false">
    <KeyDescriptor>
      <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
        <ds:X509Data>
          <ds:X509Certificate>MIIDrDCCApSgAwIBAgIJAIxzbGLou3BjMA0GCSqGSIb3DQEBBQUAMEIxCzAJBgNVBAYTAlJTMQ8wDQYDVQQIEwZTZXJiaWExDDAKBgNVBAoTA0JPUzEUMBIGA1UEAxMLbXQuZXZvLnRlYW0wHhcNMTMxMDA4MTg1OTMyWhcNMjMxMDA4MTg1OTMyWjBCMQswCQYDVQQGEwJSUzEPMA0GA1UECBMGU2VyYmlhMQwwCgYDVQQKEwNCT1MxFDASBgNVBAMTC210LmV2by50ZWFtMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAws7jML47jTQbWleRwihk15wOjuspoKPcxW1aERexAMWe8BMs1MeeTOMXjnA35breGa9PwJi2KjtDz3gkhVCglZzLZGBLLO7uchZvagFhTomZa20jTqO6JQbDli3pYNP0fBIrmEbH9cfhgm91Fm+6bTVnJ4xQhT4aPWrPAVKU2FDTBFBf4QNMIb1iI1oNErt3iocsbRTbIyjjvIe8yLVrtmZXA0DnkxB/riym0GT+4gpOEKV6GUMTF1x0eQMUzw4dkxhFs7fv6YrJymtEMmHOeiA5vVPEtxEr84JAXJyZUaZfufkj/jHUlX+POFWx2JRv+428ghrXpNvqUNqv7ozfFwIDAQABo4GkMIGhMB0GA1UdDgQWBBRomf3Xyc5ck3ceIXq0n45pxUkgwjByBgNVHSMEazBpgBRomf3Xyc5ck3ceIXq0n45pxUkgwqFGpEQwQjELMAkGA1UEBhMCUlMxDzANBgNVBAgTBlNlcmJpYTEMMAoGA1UEChMDQk9TMRQwEgYDVQQDEwttdC5ldm8udGVhbYIJAIxzbGLou3BjMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADggEBAGAXc8pe6+6owl9z2iqybE6pbjXTKqjSclMGrdeooItU1xGqBhYu/b2q6hEvYZCzlqYe5euf3r8C7GAAKEYyuwu3xuLDYV4n6l6eWTIl1doug+r0Bl8Z3157A4BcgmUT64QkekI2VDHO8WAdDOWQg1UTEoqCryTOtmRaC391iGAqbz1wtZtV95boGdur8SChK9LKcPrbCDxpo64BMgtPk2HkRgE7h5YWkLHxmxwZrYi3EAfS6IucblY3wwY4GEix8DQh1lYgpv5TOD8IMVf+oUWdp81Un/IqHqLhnSupwk6rBYbUFhN/ClK5UcoDqWHcj27tGKD6aNlxTdSwcYBl3Ts=</ds:X509Certificate>
        </ds:X509Data>
      </ds:KeyInfo>
    </KeyDescriptor>
    <Organization>
      <OrganizationName xml:lang="en-US">Organization Name</OrganizationName>
      <OrganizationDisplayName xml:lang="en-US">Display Name</OrganizationDisplayName>
      <OrganizationURL xml:lang="en-US">http://organization.org</OrganizationURL>
    </Organization>
    <ContactPerson contactType="support">
      <GivenName>Support</GivenName>
      <SurName>Smith</SurName>
      <EmailAddress>support@idp.com</EmailAddress>
    </ContactPerson>
    <SingleLogoutService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="http://idp.example.com/slo/redirect"/>
    <NameIDFormat>urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress</NameIDFormat>
    <NameIDFormat>urn:oasis:names:tc:SAML:2.0:nameid-format:persistent</NameIDFormat>
    <SingleSignOnService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="http://idp.example.com/sso/post"/>
    <SingleSignOnService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="http://idp.example.com/slo/get"/>
    <Attribute xmlns="urn:oasis:names:tc:SAML:2.0:assertion" Name="http://schemas.xmlsoap.org/claims/CommonName" FriendlyName="Common Name">
      <AttributeValue>common name value</AttributeValue>
    </Attribute>
  </IDPSSODescriptor>
  <SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol" AuthnRequestsSigned="false" WantAssertionsSigned="false">
    <SingleLogoutService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="http://sp.example.com/slo/post"/>
    <NameIDFormat>urn:oasis:names:tc:SAML:2.0:nameid-format:persistent</NameIDFormat>
    <NameIDFormat>urn:oasis:names:tc:SAML:2.0:nameid-format:transient</NameIDFormat>
    <AssertionConsumerService index="0" isDefault="true" Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="http://sp.example.com/acs/post"/>
    <AssertionConsumerService index="1" isDefault="false" Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="http://sp.example.com/acs/redirect"/>
  </SPSSODescriptor>
</EntityDescriptor>
EOT;
        $xml = trim(str_replace("\r", '', $xml));
        $expectedXml = trim(str_replace("\r", '', $expectedXml));

        $this->assertEquals($expectedXml, $xml);
    }

    public function test_deserialization()
    {
        $xml = <<<EOT
<?xml version="1.0"?>
<EntityDescriptor xmlns="urn:oasis:names:tc:SAML:2.0:metadata" entityID="http://vendor.com/id" ID="_127800fe-39ac-46ad-b073-6fb6106797a0">
  <IDPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol" WantAuthnRequestsSigned="false">
    <KeyDescriptor>
      <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
        <ds:X509Data>
          <ds:X509Certificate>MIIDrDCCApSgAwIBAgIJAIxzbGLou3BjMA0GCSqGSIb3DQEBBQUAMEIxCzAJBgNVBAYTAlJTMQ8wDQYDVQQIEwZTZXJiaWExDDAKBgNVBAoTA0JPUzEUMBIGA1UEAxMLbXQuZXZvLnRlYW0wHhcNMTMxMDA4MTg1OTMyWhcNMjMxMDA4MTg1OTMyWjBCMQswCQYDVQQGEwJSUzEPMA0GA1UECBMGU2VyYmlhMQwwCgYDVQQKEwNCT1MxFDASBgNVBAMTC210LmV2by50ZWFtMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAws7jML47jTQbWleRwihk15wOjuspoKPcxW1aERexAMWe8BMs1MeeTOMXjnA35breGa9PwJi2KjtDz3gkhVCglZzLZGBLLO7uchZvagFhTomZa20jTqO6JQbDli3pYNP0fBIrmEbH9cfhgm91Fm+6bTVnJ4xQhT4aPWrPAVKU2FDTBFBf4QNMIb1iI1oNErt3iocsbRTbIyjjvIe8yLVrtmZXA0DnkxB/riym0GT+4gpOEKV6GUMTF1x0eQMUzw4dkxhFs7fv6YrJymtEMmHOeiA5vVPEtxEr84JAXJyZUaZfufkj/jHUlX+POFWx2JRv+428ghrXpNvqUNqv7ozfFwIDAQABo4GkMIGhMB0GA1UdDgQWBBRomf3Xyc5ck3ceIXq0n45pxUkgwjByBgNVHSMEazBpgBRomf3Xyc5ck3ceIXq0n45pxUkgwqFGpEQwQjELMAkGA1UEBhMCUlMxDzANBgNVBAgTBlNlcmJpYTEMMAoGA1UEChMDQk9TMRQwEgYDVQQDEwttdC5ldm8udGVhbYIJAIxzbGLou3BjMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADggEBAGAXc8pe6+6owl9z2iqybE6pbjXTKqjSclMGrdeooItU1xGqBhYu/b2q6hEvYZCzlqYe5euf3r8C7GAAKEYyuwu3xuLDYV4n6l6eWTIl1doug+r0Bl8Z3157A4BcgmUT64QkekI2VDHO8WAdDOWQg1UTEoqCryTOtmRaC391iGAqbz1wtZtV95boGdur8SChK9LKcPrbCDxpo64BMgtPk2HkRgE7h5YWkLHxmxwZrYi3EAfS6IucblY3wwY4GEix8DQh1lYgpv5TOD8IMVf+oUWdp81Un/IqHqLhnSupwk6rBYbUFhN/ClK5UcoDqWHcj27tGKD6aNlxTdSwcYBl3Ts=</ds:X509Certificate>
        </ds:X509Data>
      </ds:KeyInfo>
    </KeyDescriptor>
    <Organization>
      <OrganizationName xml:lang="en-US">Organization Name</OrganizationName>
      <OrganizationDisplayName xml:lang="en-US">Display Name</OrganizationDisplayName>
      <OrganizationURL xml:lang="en-US">http://organization.org</OrganizationURL>
    </Organization>
    <ContactPerson contactType="support">
      <GivenName>Support</GivenName>
      <SurName>Smith</SurName>
      <EmailAddress>support@idp.com</EmailAddress>
    </ContactPerson>
    <SingleLogoutService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="http://idp.example.com/slo/redirect"/>
    <NameIDFormat>urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress</NameIDFormat>
    <NameIDFormat>urn:oasis:names:tc:SAML:2.0:nameid-format:persistent</NameIDFormat>
    <SingleSignOnService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="http://idp.example.com/sso/post"/>
    <SingleSignOnService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="http://idp.example.com/slo/get"/>
    <Attribute xmlns="urn:oasis:names:tc:SAML:2.0:assertion" Name="http://schemas.xmlsoap.org/claims/CommonName" FriendlyName="Common Name">
      <AttributeValue>common name value</AttributeValue>
    </Attribute>
  </IDPSSODescriptor>
  <SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol" AuthnRequestsSigned="false" WantAssertionsSigned="false">
    <SingleLogoutService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="http://sp.example.com/slo/post"/>
    <NameIDFormat>urn:oasis:names:tc:SAML:2.0:nameid-format:persistent</NameIDFormat>
    <NameIDFormat>urn:oasis:names:tc:SAML:2.0:nameid-format:transient</NameIDFormat>
    <AssertionConsumerService index="0" isDefault="true" Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="http://sp.example.com/acs/post"/>
    <AssertionConsumerService index="1" isDefault="false" Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="http://sp.example.com/acs/redirect"/>
  </SPSSODescriptor>
</EntityDescriptor>
EOT;

        $expectedEntityDescriptor = new EntityDescriptor();
        $expectedEntityDescriptor
            ->setEntityID($entityID = 'http://vendor.com/id')
            ->setID($edID = '_127800fe-39ac-46ad-b073-6fb6106797a0')
            ->addItem((new IdpSsoDescriptor())
                ->setWantAuthnRequestsSigned(false)
                ->addSingleSignOnService((new SingleSignOnService())
                    ->setBinding(SamlConstants::BINDING_SAML2_HTTP_POST)
                    ->setLocation('http://idp.example.com/sso/post'))
                ->addSingleSignOnService((new SingleSignOnService())
                    ->setBinding(SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
                    ->setLocation('http://idp.example.com/slo/get'))
                ->addSingleLogoutService((new SingleLogoutService())
                    ->setBinding(SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
                    ->setLocation('http://idp.example.com/slo/redirect'))
                ->addAttribute((new Attribute())
                    ->setName(ClaimTypes::COMMON_NAME)
                    ->setFriendlyName('Common Name')
                    ->addAttributeValue('common name value'))
                ->addNameIDFormat(SamlConstants::NAME_ID_FORMAT_EMAIL)
                ->addNameIDFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT)
                ->addKeyDescriptor((new KeyDescriptor())
                    ->setCertificate((new X509Certificate())
                        ->loadFromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml.crt')))
                ->addOrganization((new Organization())
                    ->setOrganizationName('Organization Name')
                    ->setOrganizationDisplayName('Display Name')
                    ->setOrganizationURL('http://organization.org'))
                ->addContactPerson((new ContactPerson())
                    ->setContactType(ContactPerson::TYPE_SUPPORT)
                    ->setGivenName('Support')
                    ->setSurName('Smith')
                    ->setEmailAddress('support@idp.com')))
            ->addItem((new SpSsoDescriptor())
                ->setAuthnRequestsSigned(false)
                ->setWantAssertionsSigned(false)
                ->addSingleLogoutService((new SingleLogoutService())
                    ->setBinding(SamlConstants::BINDING_SAML2_HTTP_POST)
                    ->setLocation('http://sp.example.com/slo/post'))
                ->addAssertionConsumerService((new AssertionConsumerService())
                    ->setBinding(SamlConstants::BINDING_SAML2_HTTP_POST)
                    ->setLocation('http://sp.example.com/acs/post')
                    ->setIndex(0)
                    ->setIsDefault(true))
                ->addAssertionConsumerService((new AssertionConsumerService())
                    ->setBinding(SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
                    ->setLocation('http://sp.example.com/acs/redirect')
                    ->setIndex(1)
                    ->setIsDefault(false))
                ->addNameIDFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT)
                ->addNameIDFormat(SamlConstants::NAME_ID_FORMAT_TRANSIENT))
        ;

        $context = new DeserializationContext();
        $context->getDocument()->loadXML($xml);

        $entityDescriptor = new EntityDescriptor();
        $entityDescriptor->deserialize($context->getDocument()->firstChild, $context);

        $this->assertEquals($expectedEntityDescriptor, $entityDescriptor);
    }
}
