<?php

require_once __DIR__.'/../autoload.php';

$entityDescriptor = new \LightSaml\Model\Metadata\EntityDescriptor();
$entityDescriptor
    ->setID(\LightSaml\Helper::generateID())
    ->setEntityID('http://some.entity.id')
;

$entityDescriptor->addItem(
    $spSsoDescriptor = (new \LightSaml\Model\Metadata\SpSsoDescriptor())
        ->setWantAssertionsSigned(true)
);

$spSsoDescriptor->addKeyDescriptor(
    $keyDescriptor = (new \LightSaml\Model\Metadata\KeyDescriptor())
        ->setUse(\LightSaml\Model\Metadata\KeyDescriptor::USE_SIGNING)
        ->setCertificate(\LightSaml\Credential\X509Certificate::fromFile('/path/to/file.crt'))
);

$spSsoDescriptor->addAssertionConsumerService(
    $acs = (new \LightSaml\Model\Metadata\AssertionConsumerService())
        ->setBinding(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_POST)
        ->setLocation('https://my.site/saml/acs')
);

$expectedSerializaedXml = <<<EOT
<EntityDescriptor ID="_2240bd9c-30c4-4d2a-ab3e-87a94ea334fd" entityID="http://some.entity.id" xmlns="urn:oasis:names:tc:SAML:2.0:metadata">
    <SPSSODescriptor WantAssertionsSigned="true" protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
        <KeyDescriptor use="signing">
            <KeyInfo xmlns="http://www.w3.org/2000/09/xmldsig#">
                <X509Data>
                    <X509Certificate>
                        MIIC0jCCAbqgAwIBAgIQGFT6omLmWbhAD65bM40rGzANBgkqhkiG9w0BAQsFADAlMSMwIQYDVQQDExpBREZTIFNpZ25pbmcgLSBCMS5iZWFkLmxvYzAeFw0xMzEwMDkxNDUyMDVaFw0xNDEwMDkxNDUyMDVaMCUxIzAhBgNVBAMTGkFERlMgU2lnbmluZyAtIEIxLmJlYWQubG9jMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlGKV64+63lpqdPmCTZ0kt/yKr8xukR1Y071SlmRVV5sSFhTe8cjylPqqxdyEBrfPhpL6vwFQyKfDhuM8T9E+BW5fUdoXO4WmIHrLOxV/BzKv2rDGidlCFzDSQPDxPH2RdQkMBksiauIMSHIYXB92rO4fkcsTgQ6cc+PZp4M3Z/jR1mcxQzz9RQk3I9w2OtI9xcv+uDC5mQU0ZWVHc99VSFQt+zshduwIqxQdHvMdTRslso+oCLEQom42pGCD8TksQTGw4sB7Ctb0mgXdfy0PDIznfi2oDBGtPY2Hkms6/n9xoyCynQea0YYXcpEe7lAvs+t6Lq+ZaKp2kUaa2x8d+QIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQBfwlmaN1iPg0gNiqdVphJjWnzpV4h6/Mz3L0xYzNQeglWCDKCKuajQfmo/AQBErtOWZJsP8avzK79gNRqFHXF6CirjGnL6WO+S6Ug1hvy3xouOxOkIYgZsbmcNL2XO1hIxP4z/QWPthotp3FSUTae2hFBHuy4Gtb+9d9a60GDtgrHnfgVeCTE7CSiaI/D/51JNbtpg2tCpcEzMQgPkQqb8E+V79xc0dnEcI5cBaS6eYgkJgS5gKIMbwaJ/VxzCVGIKwFjFnJedJ5N7zH7OVwor56Q7nuKD7X4yFY9XR3isjGnwXveh9E4d9wD4CMl52AHJpsYsToXsi3eRvApDV/PE
                    </X509Certificate>
                </X509Data>
            </KeyInfo>
        </KeyDescriptor>
        <AssertionConsumerService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="https://my.site/saml/acs"/>
    </SPSSODescriptor>
</EntityDescriptor>
EOT;
