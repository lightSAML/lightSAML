<?php

require_once __DIR__.'/../autoload.php';

$response = new \LightSaml\Model\Protocol\Response();
$response
    ->addAssertion($assertion = new \LightSaml\Model\Assertion\Assertion())
    ->setStatus(new \LightSaml\Model\Protocol\Status(new \LightSaml\Model\Protocol\StatusCode(\LightSaml\SamlConstants::STATUS_SUCCESS)))
    ->setID(\LightSaml\Helper::generateID())
    ->setIssueInstant(new \DateTime())
    ->setDestination('https://sp.com/acs')
    ->setIssuer(new \LightSaml\Model\Assertion\Issuer('https://idp.com'))
;

$assertion
    ->setId(\LightSaml\Helper::generateID())
    ->setIssueInstant(new \DateTime())
    ->setIssuer(new \LightSaml\Model\Assertion\Issuer('https://idp.com'))
    ->setSubject(
        (new \LightSaml\Model\Assertion\Subject())
            ->setNameID(new \LightSaml\Model\Assertion\NameID('email.domain.com', \LightSaml\SamlConstants::NAME_ID_FORMAT_EMAIL))
            ->addSubjectConfirmation(
                (new \LightSaml\Model\Assertion\SubjectConfirmation())
                    ->setMethod(\LightSaml\SamlConstants::CONFIRMATION_METHOD_BEARER)
                    ->setSubjectConfirmationData(
                        (new \LightSaml\Model\Assertion\SubjectConfirmationData())
                            ->setInResponseTo('id_of_the_authn_request')
                            ->setNotOnOrAfter(new \DateTime('+1 MINUTE'))
                            ->setRecipient('https://sp.com/acs')
                    )
            )
    )
    ->setConditions(
        (new \LightSaml\Model\Assertion\Conditions())
            ->setNotBefore(new \DateTime())
            ->setNotOnOrAfter(new \DateTime('+1 MINUTE'))
            ->addItem(
                new \LightSaml\Model\Assertion\AudienceRestriction(['https://sp.com/acs'])
            )
    )
    ->addItem(
        (new \LightSaml\Model\Assertion\AttributeStatement())
            ->addAttribute(new \LightSaml\Model\Assertion\Attribute(\LightSaml\ClaimTypes::EMAIL_ADDRESS, 'email@domain.com'))
            ->addAttribute(new \LightSaml\Model\Assertion\Attribute(\LightSaml\ClaimTypes::COMMON_NAME, 'x123'))
    )
    ->addItem(
        (new \LightSaml\Model\Assertion\AuthnStatement())
            ->setAuthnInstant(new \DateTime('-10 MINUTE'))
            ->setSessionIndex('_some_session_index')
            ->setAuthnContext(
                (new \LightSaml\Model\Assertion\AuthnContext())
                    ->setAuthnContextClassRef(\LightSaml\SamlConstants::AUTHN_CONTEXT_PASSWORD_PROTECTED_TRANSPORT)
            )
    )
;


$expectedXmlOutput = <<<EOT
<samlp:Response xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" ID="_8a3904146809db7b19d4eaaba9876baed805c216e5" Version="2.0"
IssueInstant="2015-10-18T20:02:55Z" Destination="https://sp.com/acs">
  <saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">https://idp.com</saml:Issuer>
  <Assertion xmlns="urn:oasis:names:tc:SAML:2.0:assertion" ID="_4a9400f18f507a46339c622929c6795c6195bd2b1d" Version="2.0" IssueInstant="2015-10-18T20:02:55Z">
    <Issuer>https://idp.com</Issuer>
    <Subject>
      <NameID Format="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress">email.domain.com</NameID>
      <SubjectConfirmation Method="urn:oasis:names:tc:SAML:2.0:cm:bearer">
        <SubjectConfirmationData InResponseTo="id_of_the_authn_request" NotOnOrAfter="2015-10-18T20:03:55Z" Recipient="https://sp.com/acs"/>
      </SubjectConfirmation>
    </Subject>
    <Conditions NotBefore="2015-10-18T20:02:55Z" NotOnOrAfter="2015-10-18T20:03:55Z">
      <AudienceRestriction>
        <Audience>https://sp.com/acs</Audience>
      </AudienceRestriction>
    </Conditions>
    <AttributeStatement>
      <Attribute Name="http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress">
        <AttributeValue>email@domain.com</AttributeValue>
      </Attribute>
      <Attribute Name="http://schemas.xmlsoap.org/claims/CommonName">
        <AttributeValue>x123</AttributeValue>
      </Attribute>
    </AttributeStatement>
    <AuthnStatement AuthnInstant="2015-10-18T19:52:55Z" SessionIndex="_some_session_index">
      <AuthnContext>
        <AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport</AuthnContextClassRef>
      </AuthnContext>
    </AuthnStatement>
  </Assertion>
</samlp:Response>
EOT;
