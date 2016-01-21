<?php

namespace LightSaml\Tests\Model\Protocol;

use LightSaml\ClaimTypes;
use LightSaml\Meta\SigningOptions;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\Attribute;
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Model\Assertion\AudienceRestriction;
use LightSaml\Model\Assertion\AuthnContext;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\Conditions;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Assertion\NameID;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Assertion\SubjectConfirmation;
use LightSaml\Model\Assertion\SubjectConfirmationData;
use LightSaml\Model\Protocol\Response;
use LightSaml\Model\Protocol\Status;
use LightSaml\Model\Protocol\StatusCode;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\SamlConstants;
use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function test_serialization()
    {
        $response = $this->getResponseObject();

        $context = new SerializationContext();
        $response->serialize($context->getDocument(), $context);
        $context->getDocument()->formatOutput = true;
        $xml = $context->getDocument()->saveXML();

        $expectedXml = <<<EOT
<?xml version="1.0"?>
<samlp:Response xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" ID="response-id" Version="2.0" IssueInstant="2013-10-27T11:55:37Z" Destination="http://destination.com" Consent="urn:oasis:names:tc:SAML:2.0:consent:unspecified" InResponseTo="in-reponse-to">
  <saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">the-issuer</saml:Issuer>
  <Assertion xmlns="urn:oasis:names:tc:SAML:2.0:assertion" ID="assertion-id" Version="2.0" IssueInstant="2013-10-27T11:55:37Z">
    <Issuer>assertion-issuer</Issuer>
    <ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
  <ds:SignedInfo><ds:CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/>
    <ds:SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/>
  <ds:Reference URI="#assertion-id"><ds:Transforms><ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/><ds:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/></ds:Transforms><ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/><ds:DigestValue>RT747opvrwnwHSTAE7rqEYX7HYM=</ds:DigestValue></ds:Reference></ds:SignedInfo><ds:SignatureValue>ql3isS1lvRZ1GBVGNTcyReGe2LS04fjvIXSqcG77KKdBiKc5RlyYRfovUguFb0WcXUWmVbteYQBp1ZBRk+SDEXZuE66OSqezv8UA7U78sw8z1ojCIrV3TlXp5mBPU5ipAM3oiZW4UcpUNcz9fdhcH+hR3/h6KRJj8UsQAXOfbNZUx2z8GkWt8eImPdTh4yBuMzrBQfiERY3DQ3vjMauHoltmbbl7V9V6duV9qrGws4QIA6uYV1BBGquuxhHM3wAbDmGTTGYIgCg6haVHk03qlLtT56YASY8nVKnTiVJV9oOj4u1IVZr8UJEUqSYgb9Wl+xYnq7SZk371o3pXLoJyww==</ds:SignatureValue>
<ds:KeyInfo><ds:X509Data><ds:X509Certificate>MIIDrDCCApSgAwIBAgIJAIxzbGLou3BjMA0GCSqGSIb3DQEBBQUAMEIxCzAJBgNVBAYTAlJTMQ8wDQYDVQQIEwZTZXJiaWExDDAKBgNVBAoTA0JPUzEUMBIGA1UEAxMLbXQuZXZvLnRlYW0wHhcNMTMxMDA4MTg1OTMyWhcNMjMxMDA4MTg1OTMyWjBCMQswCQYDVQQGEwJSUzEPMA0GA1UECBMGU2VyYmlhMQwwCgYDVQQKEwNCT1MxFDASBgNVBAMTC210LmV2by50ZWFtMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAws7jML47jTQbWleRwihk15wOjuspoKPcxW1aERexAMWe8BMs1MeeTOMXjnA35breGa9PwJi2KjtDz3gkhVCglZzLZGBLLO7uchZvagFhTomZa20jTqO6JQbDli3pYNP0fBIrmEbH9cfhgm91Fm+6bTVnJ4xQhT4aPWrPAVKU2FDTBFBf4QNMIb1iI1oNErt3iocsbRTbIyjjvIe8yLVrtmZXA0DnkxB/riym0GT+4gpOEKV6GUMTF1x0eQMUzw4dkxhFs7fv6YrJymtEMmHOeiA5vVPEtxEr84JAXJyZUaZfufkj/jHUlX+POFWx2JRv+428ghrXpNvqUNqv7ozfFwIDAQABo4GkMIGhMB0GA1UdDgQWBBRomf3Xyc5ck3ceIXq0n45pxUkgwjByBgNVHSMEazBpgBRomf3Xyc5ck3ceIXq0n45pxUkgwqFGpEQwQjELMAkGA1UEBhMCUlMxDzANBgNVBAgTBlNlcmJpYTEMMAoGA1UEChMDQk9TMRQwEgYDVQQDEwttdC5ldm8udGVhbYIJAIxzbGLou3BjMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADggEBAGAXc8pe6+6owl9z2iqybE6pbjXTKqjSclMGrdeooItU1xGqBhYu/b2q6hEvYZCzlqYe5euf3r8C7GAAKEYyuwu3xuLDYV4n6l6eWTIl1doug+r0Bl8Z3157A4BcgmUT64QkekI2VDHO8WAdDOWQg1UTEoqCryTOtmRaC391iGAqbz1wtZtV95boGdur8SChK9LKcPrbCDxpo64BMgtPk2HkRgE7h5YWkLHxmxwZrYi3EAfS6IucblY3wwY4GEix8DQh1lYgpv5TOD8IMVf+oUWdp81Un/IqHqLhnSupwk6rBYbUFhN/ClK5UcoDqWHcj27tGKD6aNlxTdSwcYBl3Ts=</ds:X509Certificate></ds:X509Data></ds:KeyInfo></ds:Signature>
    <Subject>
      <NameID Format="urn:oasis:names:tc:SAML:2.0:nameid-format:persistent">assertion-name-id</NameID>
      <SubjectConfirmation Method="urn:oasis:names:tc:SAML:2.0:cm:bearer">
        <SubjectConfirmationData InResponseTo="assertion-in-response-to" NotOnOrAfter="2013-10-27T12:00:37Z" Recipient="http://recipient.com"/>
      </SubjectConfirmation>
    </Subject>
    <Conditions NotBefore="2013-10-27T11:55:37Z" NotOnOrAfter="2013-10-27T12:55:37Z">
      <AudienceRestriction>
        <Audience>http://audience.com</Audience>
      </AudienceRestriction>
    </Conditions>
    <AttributeStatement>
      <Attribute Name="http://schemas.xmlsoap.org/claims/CommonName" FriendlyName="Common Name">
        <AttributeValue>cn value</AttributeValue>
      </Attribute>
      <Attribute Name="http://schemas.xmlsoap.org/claims/Group" FriendlyName="Group">
        <AttributeValue>group one</AttributeValue>
        <AttributeValue>group two</AttributeValue>
      </Attribute>
    </AttributeStatement>
    <AuthnStatement AuthnInstant="2013-10-27T11:55:36Z" SessionIndex="session-index">
      <AuthnContext>
        <AuthnContextClassRef>authn-context-class-ref</AuthnContextClassRef>
      </AuthnContext>
    </AuthnStatement>
  </Assertion>
</samlp:Response>
EOT;

        $xml = trim(str_replace("\r", '', $xml));
        $expectedXml = trim(str_replace("\r", '', $expectedXml));

        $this->assertEquals($expectedXml, $xml);
    }

    public function test_serialization_with_assertion_signature_subject_name()
    {
        $response = $this->getResponseObject();

        /** @var SignatureWriter $signature */
        $signature = $response->getFirstAssertion()->getSignature();
        $signature->setSigningOptions($signingOptions = new SigningOptions());
        $signingOptions->getCertificateOptions()->set(SigningOptions::CERTIFICATE_SUBJECT_NAME, true);

        $context = new SerializationContext();
        $response->serialize($context->getDocument(), $context);
        $context->getDocument()->formatOutput = true;
        $xml = $context->getDocument()->saveXML();

        $expectedXml = <<<EOT
<?xml version="1.0"?>
<samlp:Response xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" ID="response-id" Version="2.0" IssueInstant="2013-10-27T11:55:37Z" Destination="http://destination.com" Consent="urn:oasis:names:tc:SAML:2.0:consent:unspecified" InResponseTo="in-reponse-to">
  <saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">the-issuer</saml:Issuer>
  <Assertion xmlns="urn:oasis:names:tc:SAML:2.0:assertion" ID="assertion-id" Version="2.0" IssueInstant="2013-10-27T11:55:37Z">
    <Issuer>assertion-issuer</Issuer>
    <ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
  <ds:SignedInfo><ds:CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/>
    <ds:SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/>
  <ds:Reference URI="#assertion-id"><ds:Transforms><ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/><ds:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/></ds:Transforms><ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/><ds:DigestValue>RT747opvrwnwHSTAE7rqEYX7HYM=</ds:DigestValue></ds:Reference></ds:SignedInfo><ds:SignatureValue>ql3isS1lvRZ1GBVGNTcyReGe2LS04fjvIXSqcG77KKdBiKc5RlyYRfovUguFb0WcXUWmVbteYQBp1ZBRk+SDEXZuE66OSqezv8UA7U78sw8z1ojCIrV3TlXp5mBPU5ipAM3oiZW4UcpUNcz9fdhcH+hR3/h6KRJj8UsQAXOfbNZUx2z8GkWt8eImPdTh4yBuMzrBQfiERY3DQ3vjMauHoltmbbl7V9V6duV9qrGws4QIA6uYV1BBGquuxhHM3wAbDmGTTGYIgCg6haVHk03qlLtT56YASY8nVKnTiVJV9oOj4u1IVZr8UJEUqSYgb9Wl+xYnq7SZk371o3pXLoJyww==</ds:SignatureValue>
<ds:KeyInfo><ds:X509Data><ds:X509SubjectName>CN=mt.evo.team,O=BOS,ST=Serbia,C=RS</ds:X509SubjectName><ds:X509Certificate>MIIDrDCCApSgAwIBAgIJAIxzbGLou3BjMA0GCSqGSIb3DQEBBQUAMEIxCzAJBgNVBAYTAlJTMQ8wDQYDVQQIEwZTZXJiaWExDDAKBgNVBAoTA0JPUzEUMBIGA1UEAxMLbXQuZXZvLnRlYW0wHhcNMTMxMDA4MTg1OTMyWhcNMjMxMDA4MTg1OTMyWjBCMQswCQYDVQQGEwJSUzEPMA0GA1UECBMGU2VyYmlhMQwwCgYDVQQKEwNCT1MxFDASBgNVBAMTC210LmV2by50ZWFtMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAws7jML47jTQbWleRwihk15wOjuspoKPcxW1aERexAMWe8BMs1MeeTOMXjnA35breGa9PwJi2KjtDz3gkhVCglZzLZGBLLO7uchZvagFhTomZa20jTqO6JQbDli3pYNP0fBIrmEbH9cfhgm91Fm+6bTVnJ4xQhT4aPWrPAVKU2FDTBFBf4QNMIb1iI1oNErt3iocsbRTbIyjjvIe8yLVrtmZXA0DnkxB/riym0GT+4gpOEKV6GUMTF1x0eQMUzw4dkxhFs7fv6YrJymtEMmHOeiA5vVPEtxEr84JAXJyZUaZfufkj/jHUlX+POFWx2JRv+428ghrXpNvqUNqv7ozfFwIDAQABo4GkMIGhMB0GA1UdDgQWBBRomf3Xyc5ck3ceIXq0n45pxUkgwjByBgNVHSMEazBpgBRomf3Xyc5ck3ceIXq0n45pxUkgwqFGpEQwQjELMAkGA1UEBhMCUlMxDzANBgNVBAgTBlNlcmJpYTEMMAoGA1UEChMDQk9TMRQwEgYDVQQDEwttdC5ldm8udGVhbYIJAIxzbGLou3BjMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADggEBAGAXc8pe6+6owl9z2iqybE6pbjXTKqjSclMGrdeooItU1xGqBhYu/b2q6hEvYZCzlqYe5euf3r8C7GAAKEYyuwu3xuLDYV4n6l6eWTIl1doug+r0Bl8Z3157A4BcgmUT64QkekI2VDHO8WAdDOWQg1UTEoqCryTOtmRaC391iGAqbz1wtZtV95boGdur8SChK9LKcPrbCDxpo64BMgtPk2HkRgE7h5YWkLHxmxwZrYi3EAfS6IucblY3wwY4GEix8DQh1lYgpv5TOD8IMVf+oUWdp81Un/IqHqLhnSupwk6rBYbUFhN/ClK5UcoDqWHcj27tGKD6aNlxTdSwcYBl3Ts=</ds:X509Certificate></ds:X509Data></ds:KeyInfo></ds:Signature>
    <Subject>
      <NameID Format="urn:oasis:names:tc:SAML:2.0:nameid-format:persistent">assertion-name-id</NameID>
      <SubjectConfirmation Method="urn:oasis:names:tc:SAML:2.0:cm:bearer">
        <SubjectConfirmationData InResponseTo="assertion-in-response-to" NotOnOrAfter="2013-10-27T12:00:37Z" Recipient="http://recipient.com"/>
      </SubjectConfirmation>
    </Subject>
    <Conditions NotBefore="2013-10-27T11:55:37Z" NotOnOrAfter="2013-10-27T12:55:37Z">
      <AudienceRestriction>
        <Audience>http://audience.com</Audience>
      </AudienceRestriction>
    </Conditions>
    <AttributeStatement>
      <Attribute Name="http://schemas.xmlsoap.org/claims/CommonName" FriendlyName="Common Name">
        <AttributeValue>cn value</AttributeValue>
      </Attribute>
      <Attribute Name="http://schemas.xmlsoap.org/claims/Group" FriendlyName="Group">
        <AttributeValue>group one</AttributeValue>
        <AttributeValue>group two</AttributeValue>
      </Attribute>
    </AttributeStatement>
    <AuthnStatement AuthnInstant="2013-10-27T11:55:36Z" SessionIndex="session-index">
      <AuthnContext>
        <AuthnContextClassRef>authn-context-class-ref</AuthnContextClassRef>
      </AuthnContext>
    </AuthnStatement>
  </Assertion>
</samlp:Response>
EOT;

        $xml = trim(str_replace("\r", '', $xml));
        $expectedXml = trim(str_replace("\r", '', $expectedXml));

        $this->assertEquals($expectedXml, $xml);
    }

    public function test_serialization_with_assertion_signature_issuer_serial()
    {
        $response = $this->getResponseObject();

        /** @var SignatureWriter $signature */
        $signature = $response->getFirstAssertion()->getSignature();
        $signature->setSigningOptions($signingOptions = new SigningOptions());
        $signingOptions->getCertificateOptions()->set(SigningOptions::CERTIFICATE_ISSUER_SERIAL, true);

        $context = new SerializationContext();
        $response->serialize($context->getDocument(), $context);
        $context->getDocument()->formatOutput = true;
        $xml = $context->getDocument()->saveXML();

        $expectedXml = <<<EOT
<?xml version="1.0"?>
<samlp:Response xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" ID="response-id" Version="2.0" IssueInstant="2013-10-27T11:55:37Z" Destination="http://destination.com" Consent="urn:oasis:names:tc:SAML:2.0:consent:unspecified" InResponseTo="in-reponse-to">
  <saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">the-issuer</saml:Issuer>
  <Assertion xmlns="urn:oasis:names:tc:SAML:2.0:assertion" ID="assertion-id" Version="2.0" IssueInstant="2013-10-27T11:55:37Z">
    <Issuer>assertion-issuer</Issuer>
    <ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
  <ds:SignedInfo><ds:CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/>
    <ds:SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/>
  <ds:Reference URI="#assertion-id"><ds:Transforms><ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/><ds:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/></ds:Transforms><ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/><ds:DigestValue>RT747opvrwnwHSTAE7rqEYX7HYM=</ds:DigestValue></ds:Reference></ds:SignedInfo><ds:SignatureValue>ql3isS1lvRZ1GBVGNTcyReGe2LS04fjvIXSqcG77KKdBiKc5RlyYRfovUguFb0WcXUWmVbteYQBp1ZBRk+SDEXZuE66OSqezv8UA7U78sw8z1ojCIrV3TlXp5mBPU5ipAM3oiZW4UcpUNcz9fdhcH+hR3/h6KRJj8UsQAXOfbNZUx2z8GkWt8eImPdTh4yBuMzrBQfiERY3DQ3vjMauHoltmbbl7V9V6duV9qrGws4QIA6uYV1BBGquuxhHM3wAbDmGTTGYIgCg6haVHk03qlLtT56YASY8nVKnTiVJV9oOj4u1IVZr8UJEUqSYgb9Wl+xYnq7SZk371o3pXLoJyww==</ds:SignatureValue>
<ds:KeyInfo><ds:X509Data><ds:X509IssuerSerial><ds:X509IssuerName>CN=mt.evo.team,O=BOS,ST=Serbia,C=RS</ds:X509IssuerName><ds:X509SerialNumber>10120551959698829411</ds:X509SerialNumber></ds:X509IssuerSerial><ds:X509Certificate>MIIDrDCCApSgAwIBAgIJAIxzbGLou3BjMA0GCSqGSIb3DQEBBQUAMEIxCzAJBgNVBAYTAlJTMQ8wDQYDVQQIEwZTZXJiaWExDDAKBgNVBAoTA0JPUzEUMBIGA1UEAxMLbXQuZXZvLnRlYW0wHhcNMTMxMDA4MTg1OTMyWhcNMjMxMDA4MTg1OTMyWjBCMQswCQYDVQQGEwJSUzEPMA0GA1UECBMGU2VyYmlhMQwwCgYDVQQKEwNCT1MxFDASBgNVBAMTC210LmV2by50ZWFtMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAws7jML47jTQbWleRwihk15wOjuspoKPcxW1aERexAMWe8BMs1MeeTOMXjnA35breGa9PwJi2KjtDz3gkhVCglZzLZGBLLO7uchZvagFhTomZa20jTqO6JQbDli3pYNP0fBIrmEbH9cfhgm91Fm+6bTVnJ4xQhT4aPWrPAVKU2FDTBFBf4QNMIb1iI1oNErt3iocsbRTbIyjjvIe8yLVrtmZXA0DnkxB/riym0GT+4gpOEKV6GUMTF1x0eQMUzw4dkxhFs7fv6YrJymtEMmHOeiA5vVPEtxEr84JAXJyZUaZfufkj/jHUlX+POFWx2JRv+428ghrXpNvqUNqv7ozfFwIDAQABo4GkMIGhMB0GA1UdDgQWBBRomf3Xyc5ck3ceIXq0n45pxUkgwjByBgNVHSMEazBpgBRomf3Xyc5ck3ceIXq0n45pxUkgwqFGpEQwQjELMAkGA1UEBhMCUlMxDzANBgNVBAgTBlNlcmJpYTEMMAoGA1UEChMDQk9TMRQwEgYDVQQDEwttdC5ldm8udGVhbYIJAIxzbGLou3BjMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADggEBAGAXc8pe6+6owl9z2iqybE6pbjXTKqjSclMGrdeooItU1xGqBhYu/b2q6hEvYZCzlqYe5euf3r8C7GAAKEYyuwu3xuLDYV4n6l6eWTIl1doug+r0Bl8Z3157A4BcgmUT64QkekI2VDHO8WAdDOWQg1UTEoqCryTOtmRaC391iGAqbz1wtZtV95boGdur8SChK9LKcPrbCDxpo64BMgtPk2HkRgE7h5YWkLHxmxwZrYi3EAfS6IucblY3wwY4GEix8DQh1lYgpv5TOD8IMVf+oUWdp81Un/IqHqLhnSupwk6rBYbUFhN/ClK5UcoDqWHcj27tGKD6aNlxTdSwcYBl3Ts=</ds:X509Certificate></ds:X509Data></ds:KeyInfo></ds:Signature>
    <Subject>
      <NameID Format="urn:oasis:names:tc:SAML:2.0:nameid-format:persistent">assertion-name-id</NameID>
      <SubjectConfirmation Method="urn:oasis:names:tc:SAML:2.0:cm:bearer">
        <SubjectConfirmationData InResponseTo="assertion-in-response-to" NotOnOrAfter="2013-10-27T12:00:37Z" Recipient="http://recipient.com"/>
      </SubjectConfirmation>
    </Subject>
    <Conditions NotBefore="2013-10-27T11:55:37Z" NotOnOrAfter="2013-10-27T12:55:37Z">
      <AudienceRestriction>
        <Audience>http://audience.com</Audience>
      </AudienceRestriction>
    </Conditions>
    <AttributeStatement>
      <Attribute Name="http://schemas.xmlsoap.org/claims/CommonName" FriendlyName="Common Name">
        <AttributeValue>cn value</AttributeValue>
      </Attribute>
      <Attribute Name="http://schemas.xmlsoap.org/claims/Group" FriendlyName="Group">
        <AttributeValue>group one</AttributeValue>
        <AttributeValue>group two</AttributeValue>
      </Attribute>
    </AttributeStatement>
    <AuthnStatement AuthnInstant="2013-10-27T11:55:36Z" SessionIndex="session-index">
      <AuthnContext>
        <AuthnContextClassRef>authn-context-class-ref</AuthnContextClassRef>
      </AuthnContext>
    </AuthnStatement>
  </Assertion>
</samlp:Response>
EOT;

        $xml = trim(str_replace("\r", '', $xml));
        $expectedXml = trim(str_replace("\r", '', $expectedXml));

        $this->assertEquals($expectedXml, $xml);
    }

    /**
     * @return Response
     */
    private function getResponseObject()
    {
        $response = new Response();
        $response->setId('response-id')
            ->setIssueInstant('2013-10-27T11:55:37Z')
            ->setDestination('http://destination.com')
            ->setConsent(SamlConstants::CONSENT_UNSPECIFIED)
            ->setInResponseTo('in-reponse-to')
            ->addAssertion((new Assertion())
                ->setId('assertion-id')
                ->setIssueInstant('2013-10-27T11:55:37Z')
                ->setIssuer((new Issuer())
                    ->setValue('assertion-issuer'))
                ->setSubject((new Subject())
                    ->setNameID((new NameID())
                        ->setValue('assertion-name-id')
                        ->setFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT))
                    ->addSubjectConfirmation((new SubjectConfirmation())
                        ->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER)
                        ->setSubjectConfirmationData((new SubjectConfirmationData())
                            ->setInResponseTo('assertion-in-response-to')
                            ->setNotOnOrAfter('2013-10-27T12:00:37Z')
                            ->setRecipient('http://recipient.com'))))
                ->setConditions((new Conditions())
                    ->setNotBefore('2013-10-27T11:55:37Z')
                    ->setNotOnOrAfter('2013-10-27T12:55:37Z')
                    ->addItem((new AudienceRestriction())
                        ->addAudience('http://audience.com')))
                ->addItem((new AttributeStatement())
                    ->addAttribute((new Attribute())
                        ->setName(ClaimTypes::COMMON_NAME)
                        ->setFriendlyName('Common Name')
                        ->addAttributeValue('cn value'))
                    ->addAttribute((new Attribute())
                        ->setName(ClaimTypes::GROUP)
                        ->setFriendlyName('Group')
                        ->addAttributeValue('group one')
                        ->addAttributeValue('group two')))
                ->addItem((new AuthnStatement())
                    ->setAuthnInstant('2013-10-27T11:55:36Z')
                    ->setSessionIndex('session-index')
                    ->setAuthnContext((new AuthnContext())
                        ->setAuthnContextClassRef('authn-context-class-ref')))
                ->setSignature(new SignatureWriter(
                    X509Certificate::fromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml.crt'),
                    KeyHelper::createPrivateKey(
                        __DIR__.'/../../../../../resources/sample/Certificate/saml.pem',
                        '',
                        true
                    )
                ))
            )
            ->setIssuer((new Issuer())
                ->setValue('the-issuer'))
        ;

        return $response;
    }
}
