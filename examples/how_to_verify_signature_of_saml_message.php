<?php

require_once __DIR__.'/../autoload.php';

$xml = <<<EOT
<?xml version="1.0"?>
<AuthnRequest xmlns="urn:oasis:names:tc:SAML:2.0:protocol" ID="_894da3368874d2dd637983b6812f66c444f100f205" Version="2.0" IssueInstant="2015-09-13T11:47:33Z" Destination="https://idp.testshib.org/idp/profile/SAML2/POST/SSO"><saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" Format="urn:oasis:names:tc:SAML:2.0:nameid-format:entity">https://mt.evo.loc/sp</saml:Issuer><ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
  <ds:SignedInfo><ds:CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/>
    <ds:SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/>
  <ds:Reference URI="#_894da3368874d2dd637983b6812f66c444f100f205"><ds:Transforms><ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/><ds:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/></ds:Transforms><ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/><ds:DigestValue>94dChUrRo35DfipIGNBVil4Qip8=</ds:DigestValue></ds:Reference></ds:SignedInfo><ds:SignatureValue>rjtDDEZN4T2L4Xw5W5ijALoambKl85HsBGy/pFlmk6b7JqSVq8wJJkrq6D5nxUPzNf7B+L2wju1M98stmUhvYCtU2cHRE6wjKwa7tsumYDxuOBQ4ufBt09TJtjogny5ikzCtb2csOoQjosExmVw3f2J+FkLl4rjY6Ngwlsnpn0AttqNdtykAdwuIE3BmXKhMTxelPhxMZ9bCOoODlgU568E+3KuOxmcf85e+uGIApuxnzTZX62MlnVtsveMQdb0VT4AKJhVbFIb7sW+UwMQWhznWhjdnhIz65CHTnBUMzLyOilugwE5Rvk79fPqeGDNrNyeh+3Fhko+GAj0lNluyWA==</ds:SignatureValue>
<ds:KeyInfo><ds:X509Data><ds:X509Certificate>MIIDyjCCArKgAwIBAgIJANZLMiMszO+tMA0GCSqGSIb3DQEBBQUAMEwxCzAJBgNVBAYTAlJTMREwDwYDVQQIEwhCZWxncmFkZTESMBAGA1UEChMJTGlnaHRTQU1MMRYwFAYDVQQDEw1saWdodHNhbWwuY29tMB4XDTE1MDkxMzE4MzU0NloXDTI1MDkxMDE4MzU0NlowTDELMAkGA1UEBhMCUlMxETAPBgNVBAgTCEJlbGdyYWRlMRIwEAYDVQQKEwlMaWdodFNBTUwxFjAUBgNVBAMTDWxpZ2h0c2FtbC5jb20wggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQC7pUKOPMyE2oScHLPGJFTepK9j1H03e/s/WnONw8ZwYBaBIYIQuX6uE8jFPdD0uQSaYpOw5h5Tgq6xBV7m2kPO53hs8gEGWRbCdCtxi9EMJwIOYr+isG0N+DvV9KybJf6tqcM50PiFjVNtfx8IubMpAKCbquaqdLaHH0rgP1hbgnGm5YZkyEK4s8xuLUDS6qL7N7a/ez2Zk45u3L3qFcuncPI5BTnJg6fqlypDhCDOBI5Ljw10HmgZHPIXzOhEPVV+rX2iHhF4V9vzEoeIUABYXQVNRRNHpPdVsK6iTTkyvbrGJ/tv3oFZhNOSL0Kuy+Q9nlE9fEFqyUydJ67vsXqZAgMBAAGjga4wgaswHQYDVR0OBBYEFHPT6Ey1qgxMzMIt2d3OWuwzfPSUMHwGA1UdIwR1MHOAFHPT6Ey1qgxMzMIt2d3OWuwzfPSUoVCkTjBMMQswCQYDVQQGEwJSUzERMA8GA1UECBMIQmVsZ3JhZGUxEjAQBgNVBAoTCUxpZ2h0U0FNTDEWMBQGA1UEAxMNbGlnaHRzYW1sLmNvbYIJANZLMiMszO+tMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADggEBAE0HxNZpi/gSVrkhQ756AgIC25l6A4C6xZ8iAZiBApJcVdUZytBgpzypFSd8yg7Yh5P3ftlDjYEMB/uIvBsKe6HQyUy90VrSi4aaGC/7ilj6DTCX3jeuuH1JnU6sBxhN9IiJRY3DbMzY5KAdtK/1fYlKa6PugXruJWrB3bC1VaFWLjMytnvaEQxjam4bsj1sF0+v6jL3RIQzdW9jJ7Udoul5fGR56A0Uhi0lqObPKI2lIK1psWXLwksdvO9NNt9Vm27QLlklvpYuIh086wLmbiVmO+VQxDYwPmL8NEiLSA4Po/q7n+qV7Vx/EtIKr7lwZ2Micv5Xm0sequAbt3dnqPI=</ds:X509Certificate></ds:X509Data></ds:KeyInfo></ds:Signature></AuthnRequest>
EOT;

$deserializationContext = new \LightSaml\Model\Context\DeserializationContext();
$deserializationContext->getDocument()->loadXML($xml);
$authnRequest = new \LightSaml\Model\Protocol\AuthnRequest();
$authnRequest->deserialize($deserializationContext->getDocument()->firstChild, $deserializationContext);

$key = \LightSaml\Credential\KeyHelper::createPublicKey(
    \LightSaml\Credential\X509Certificate::fromFile(__DIR__.'/../web/sp/saml.crt')
);

/** @var \LightSaml\Model\XmlDSig\SignatureXmlReader $signatureReader */
$signatureReader = $authnRequest->getSignature();

try {
    $ok = $signatureReader->validate($key);

    if ($ok) {
        print "Signaure OK\n";
    } else {
        print "Signature not validated";
    }
} catch (\Exception $ex) {
    print "Signature validation failed\n";
}
