<?php

require_once __DIR__.'/../autoload.php';

$deserializationContext = new \LightSaml\Model\Context\DeserializationContext();

$xml = <<<EOT
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

$deserializationContext->getDocument()->loadXML($xml);

$authnRequest = new \LightSaml\Model\Protocol\AuthnRequest();

$authnRequest->deserialize($deserializationContext->getDocument()->firstChild, $deserializationContext);

var_dump($authnRequest);
