<?php

require_once __DIR__.'/../autoload.php';

$authnRequest = new \LightSaml\Model\Protocol\AuthnRequest();
$authnRequest
    ->setAssertionConsumerServiceURL('https://my.site/acs')
    ->setProtocolBinding(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_POST)
    ->setID(\LightSaml\Helper::generateID())
    ->setIssueInstant(new \DateTime())
    ->setDestination('https://idp.com/login')
    ->setIssuer(new \LightSaml\Model\Assertion\Issuer('https://my.entity.id'))
;

$expectedXmlOutput = <<<EOT
<samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="_8dcc6985f6d9f385f0bbd4562ef848ef3ae78d87d7"
    Version="2.0"
    IssueInstant="2015-10-10T15:26:20Z"
    Destination="https://idp.com/login"
    AssertionConsumerServiceURL="https://my.site/acs"
    ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
>
    <saml:Issuer>https://my.entity.id</saml:Issuer>
</samlp:AuthnRequest>
EOT;
