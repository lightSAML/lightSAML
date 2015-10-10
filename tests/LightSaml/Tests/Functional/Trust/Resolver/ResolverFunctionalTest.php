<?php

namespace LightSaml\Tests\Functional\Trust\Resolver;

use LightSaml\Resolver\Credential\Factory\CredentialResolverFactory;
use LightSaml\Store\EntityDescriptor\FixedEntityDescriptorStore;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\SamlConstants;
use LightSaml\Store\Credential\CompositeCredentialStore;
use LightSaml\Store\Credential\MetadataCredentialStore;
use LightSaml\Store\Credential\StaticCredentialStore;
use LightSaml\Credential\UsageType;
use LightSaml\Credential\X509Credential;
use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Credential\Criteria\EntityIdCriteria;
use LightSaml\Credential\Criteria\MetadataCriteria;
use LightSaml\Credential\Criteria\UsageCriteria;

class ResolverFunctionalTest extends \PHPUnit_Framework_TestCase
{
    public function test__idp2()
    {
        $resolver = $this->getResolver();

        $set = (new CriteriaSet())
            ->add(new EntityIdCriteria($entityId = 'https://B1.bead.loc/adfs/services/trust'))
            ->add(new MetadataCriteria(MetadataCriteria::TYPE_IDP, SamlConstants::PROTOCOL_SAML2))
            ->add(new UsageCriteria(UsageType::SIGNING))
        ;

        $arrCredentials = $resolver->resolve($set);

        $this->assertCount(1, $arrCredentials);

        $credential = $arrCredentials[0];

        $this->assertEquals($entityId, $credential->getEntityId());
        $crt = new X509Certificate();
        $crt->loadPem($credential->getPublicKey()->getX509Certificate());
        $this->assertEquals(
            'MIIC0jCCAbqgAwIBAgIQGFT6omLmWbhAD65bM40rGzANBgkqhkiG9w0BAQsFADAlMSMwIQYDVQQDExpBREZTIFNpZ25pbmcgLSBCMS5iZWFkLmxvYzAeFw0xMzEwMDkxNDUyMDVaFw0xNDEwMDkxNDUyMDVaMCUxIzAhBgNVBAMTGkFERlMgU2lnbmluZyAtIEIxLmJlYWQubG9jMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlGKV64+63lpqdPmCTZ0kt/yKr8xukR1Y071SlmRVV5sSFhTe8cjylPqqxdyEBrfPhpL6vwFQyKfDhuM8T9E+BW5fUdoXO4WmIHrLOxV/BzKv2rDGidlCFzDSQPDxPH2RdQkMBksiauIMSHIYXB92rO4fkcsTgQ6cc+PZp4M3Z/jR1mcxQzz9RQk3I9w2OtI9xcv+uDC5mQU0ZWVHc99VSFQt+zshduwIqxQdHvMdTRslso+oCLEQom42pGCD8TksQTGw4sB7Ctb0mgXdfy0PDIznfi2oDBGtPY2Hkms6/n9xoyCynQea0YYXcpEe7lAvs+t6Lq+ZaKp2kUaa2x8d+QIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQBfwlmaN1iPg0gNiqdVphJjWnzpV4h6/Mz3L0xYzNQeglWCDKCKuajQfmo/AQBErtOWZJsP8avzK79gNRqFHXF6CirjGnL6WO+S6Ug1hvy3xouOxOkIYgZsbmcNL2XO1hIxP4z/QWPthotp3FSUTae2hFBHuy4Gtb+9d9a60GDtgrHnfgVeCTE7CSiaI/D/51JNbtpg2tCpcEzMQgPkQqb8E+V79xc0dnEcI5cBaS6eYgkJgS5gKIMbwaJ/VxzCVGIKwFjFnJedJ5N7zH7OVwor56Q7nuKD7X4yFY9XR3isjGnwXveh9E4d9wD4CMl52AHJpsYsToXsi3eRvApDV/PE',
            $crt->getData()
        );

        /** @var \LightSaml\Credential\Context\MetadataCredentialContext $metadataContext */
        $metadataContext = $credential->getCredentialContext()->get('LightSaml\Credential\Context\MetadataCredentialContext');
        $this->assertNotNull($metadataContext);
        $this->assertInstanceOf('LightSaml\Model\Metadata\IdpSsoDescriptor', $metadataContext->getRoleDescriptor());

        $this->assertEquals(UsageType::SIGNING, $credential->getUsageType());
    }

    public function test__idp()
    {
        $resolver = $this->getResolver();

        $set = (new CriteriaSet())
            ->add(new EntityIdCriteria($entityId = 'https://sts.windows.net/554fadfe-f04f-4975-90cb-ddc8b147aaa2/'))
            ->add(new MetadataCriteria(MetadataCriteria::TYPE_IDP, SamlConstants::PROTOCOL_SAML2))
            ->add(new UsageCriteria(UsageType::SIGNING))
        ;

        $arrCredentials = $resolver->resolve($set);

        $this->assertCount(1, $arrCredentials);

        $credential = $arrCredentials[0];

        $this->assertEquals($entityId, $credential->getEntityId());
        $crt = new X509Certificate();
        $crt->loadPem($credential->getPublicKey()->getX509Certificate());
        $this->assertEquals(
            'MIIDPjCCAiqgAwIBAgIQVWmXY/+9RqFA/OG9kFulHDAJBgUrDgMCHQUAMC0xKzApBgNVBAMTImFjY291bnRzLmFjY2Vzc2NvbnRyb2wud2luZG93cy5uZXQwHhcNMTIwNjA3MDcwMDAwWhcNMTQwNjA3MDcwMDAwWjAtMSswKQYDVQQDEyJhY2NvdW50cy5hY2Nlc3Njb250cm9sLndpbmRvd3MubmV0MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArCz8Sn3GGXmikH2MdTeGY1D711EORX/lVXpr+ecGgqfUWF8MPB07XkYuJ54DAuYT318+2XrzMjOtqkT94VkXmxv6dFGhG8YZ8vNMPd4tdj9c0lpvWQdqXtL1TlFRpD/P6UMEigfN0c9oWDg9U7Ilymgei0UXtf1gtcQbc5sSQU0S4vr9YJp2gLFIGK11Iqg4XSGdcI0QWLLkkC6cBukhVnd6BCYbLjTYy3fNs4DzNdemJlxGl8sLexFytBF6YApvSdus3nFXaMCtBGx16HzkK9ne3lobAwL2o79bP4imEGqg+ibvyNmbrwFGnQrBc1jTF9LyQX9q+louxVfHs6ZiVwIDAQABo2IwYDBeBgNVHQEEVzBVgBCxDDsLd8xkfOLKm4Q/SzjtoS8wLTErMCkGA1UEAxMiYWNjb3VudHMuYWNjZXNzY29udHJvbC53aW5kb3dzLm5ldIIQVWmXY/+9RqFA/OG9kFulHDAJBgUrDgMCHQUAA4IBAQAkJtxxm/ErgySlNk69+1odTMP8Oy6L0H17z7XGG3w4TqvTUSWaxD4hSFJ0e7mHLQLQD7oV/erACXwSZn2pMoZ89MBDjOMQA+e6QzGB7jmSzPTNmQgMLA8fWCfqPrz6zgH+1F1gNp8hJY57kfeVPBiyjuBmlTEBsBlzolY9dd/55qqfQk6cgSeCbHCy/RU/iep0+UsRMlSgPNNmqhj5gmN2AFVCN96zF694LwuPae5CeR2ZcVknexOWHYjFM0MgUSw0ubnGl0h9AJgGyhvNGcjQqu9vd1xkupFgaN+f7P3p3EVN5csBg5H94jEcQZT7EKeTiZ6bTrpDAnrr8tDCy8ng',
            $crt->getData()
        );

        /** @var \LightSaml\Credential\Context\MetadataCredentialContext $metadataContext */
        $metadataContext = $credential->getCredentialContext()->get('LightSaml\Credential\Context\MetadataCredentialContext');
        $this->assertNotNull($metadataContext);
        $this->assertInstanceOf('LightSaml\Model\Metadata\IdpSsoDescriptor', $metadataContext->getRoleDescriptor());

        $this->assertEquals(UsageType::SIGNING, $credential->getUsageType());
    }

    public function test__sp2()
    {
        $resolver = $this->getResolver();

        $set = (new CriteriaSet())
            ->add(new EntityIdCriteria($entityId = 'https://mt.evo.team/simplesaml/module.php/saml/sp/metadata.php/default-sp'))
            ->add(new MetadataCriteria(MetadataCriteria::TYPE_SP, SamlConstants::PROTOCOL_SAML2))
            ->add(new UsageCriteria(UsageType::SIGNING))
        ;

        $arrCredentials = $resolver->resolve($set);

        $this->assertCount(1, $arrCredentials);

        $credential = $arrCredentials[0];

        $this->assertEquals($entityId, $credential->getEntityId());
        $crt = new X509Certificate();
        $crt->loadPem($credential->getPublicKey()->getX509Certificate());
        $this->assertEquals(
            'MIIDrDCCApSgAwIBAgIJAIxzbGLou3BjMA0GCSqGSIb3DQEBBQUAMEIxCzAJBgNVBAYTAlJTMQ8wDQYDVQQIEwZTZXJiaWExDDAKBgNVBAoTA0JPUzEUMBIGA1UEAxMLbXQuZXZvLnRlYW0wHhcNMTMxMDA4MTg1OTMyWhcNMjMxMDA4MTg1OTMyWjBCMQswCQYDVQQGEwJSUzEPMA0GA1UECBMGU2VyYmlhMQwwCgYDVQQKEwNCT1MxFDASBgNVBAMTC210LmV2by50ZWFtMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAws7jML47jTQbWleRwihk15wOjuspoKPcxW1aERexAMWe8BMs1MeeTOMXjnA35breGa9PwJi2KjtDz3gkhVCglZzLZGBLLO7uchZvagFhTomZa20jTqO6JQbDli3pYNP0fBIrmEbH9cfhgm91Fm+6bTVnJ4xQhT4aPWrPAVKU2FDTBFBf4QNMIb1iI1oNErt3iocsbRTbIyjjvIe8yLVrtmZXA0DnkxB/riym0GT+4gpOEKV6GUMTF1x0eQMUzw4dkxhFs7fv6YrJymtEMmHOeiA5vVPEtxEr84JAXJyZUaZfufkj/jHUlX+POFWx2JRv+428ghrXpNvqUNqv7ozfFwIDAQABo4GkMIGhMB0GA1UdDgQWBBRomf3Xyc5ck3ceIXq0n45pxUkgwjByBgNVHSMEazBpgBRomf3Xyc5ck3ceIXq0n45pxUkgwqFGpEQwQjELMAkGA1UEBhMCUlMxDzANBgNVBAgTBlNlcmJpYTEMMAoGA1UEChMDQk9TMRQwEgYDVQQDEwttdC5ldm8udGVhbYIJAIxzbGLou3BjMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADggEBAGAXc8pe6+6owl9z2iqybE6pbjXTKqjSclMGrdeooItU1xGqBhYu/b2q6hEvYZCzlqYe5euf3r8C7GAAKEYyuwu3xuLDYV4n6l6eWTIl1doug+r0Bl8Z3157A4BcgmUT64QkekI2VDHO8WAdDOWQg1UTEoqCryTOtmRaC391iGAqbz1wtZtV95boGdur8SChK9LKcPrbCDxpo64BMgtPk2HkRgE7h5YWkLHxmxwZrYi3EAfS6IucblY3wwY4GEix8DQh1lYgpv5TOD8IMVf+oUWdp81Un/IqHqLhnSupwk6rBYbUFhN/ClK5UcoDqWHcj27tGKD6aNlxTdSwcYBl3Ts=',
            $crt->getData()
        );

        /** @var \LightSaml\Credential\Context\MetadataCredentialContext $metadataContext */
        $metadataContext = $credential->getCredentialContext()->get('LightSaml\Credential\Context\MetadataCredentialContext');
        $this->assertNotNull($metadataContext);
        $this->assertInstanceOf('LightSaml\Model\Metadata\SpSsoDescriptor', $metadataContext->getRoleDescriptor());

        $this->assertEquals(UsageType::SIGNING, $credential->getUsageType());
    }

    public function test__get_private_key()
    {
        $resolver = $this->getResolver();

        $set = (new CriteriaSet())
            ->add(new EntityIdCriteria($entityId = 'https://mt.evo.loc/sp'))
            ->add(new MetadataCriteria(MetadataCriteria::TYPE_SP, SamlConstants::PROTOCOL_SAML2))
            ->add(new UsageCriteria(UsageType::ENCRYPTION))
        ;

        $arrCredentials = $resolver->resolve($set);

        $this->assertCount(1, $arrCredentials);

        $credential = $arrCredentials[0];

        $this->assertNotNull($credential->getPrivateKey());
    }

    /**
     * @return \LightSaml\Resolver\Credential\CredentialResolverInterface
     */
    private function getResolver()
    {
        $provider = new FixedEntityDescriptorStore();
        $provider->add(EntityDescriptor::load(__DIR__.'/../../../../../../resources/sample/EntityDescriptor/idp2-ed.xml'));
        $provider->add(EntityDescriptor::load(__DIR__.'/../../../../../../resources/sample/EntityDescriptor/idp-ed.xml'));
        $provider->add(EntityDescriptor::load(__DIR__.'/../../../../../../resources/sample/EntityDescriptor/ed01-formatted-certificate.xml'));
        $provider->add(EntityDescriptor::load(__DIR__.'/../../../../../../resources/sample/EntityDescriptor/sp-ed2.xml'));

        $metadataStore = new MetadataCredentialStore($provider);

        $certificate = new X509Certificate();
        $certificate->loadFromFile(__DIR__.'/../../../../../../resources/sample/Certificate/saml.crt');

        $credential = new X509Credential(
            $certificate,
            KeyHelper::createPrivateKey(__DIR__.'/../../../../../../resources/sample/Certificate/saml.pem', '', true)
        );
        $credential
            ->setUsageType(UsageType::ENCRYPTION)
            ->setEntityId('https://mt.evo.loc/sp')
        ;

        $staticStore = new StaticCredentialStore();
        $staticStore->add($credential);

        $compositeStore = new CompositeCredentialStore();
        $compositeStore->add($metadataStore)->add($staticStore);

        $resolverFactory = new CredentialResolverFactory($compositeStore);
        $resolver = $resolverFactory->build();

        return $resolver;
    }
}
