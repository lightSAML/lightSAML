<?php

namespace LightSaml\Tests\Functional\Credential;

use LightSaml\Credential\X509Certificate;
use LightSaml\SamlConstants;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class X509CertificateTest extends \PHPUnit_Framework_TestCase
{
    public function test_get_name()
    {
        $certificate = new X509Certificate();
        $certificate->loadFromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml.crt');
        $this->assertEquals('/C=RS/ST=Serbia/O=BOS/CN=mt.evo.team', $certificate->getName());
    }

    public function test_algorithm_sha1()
    {
        $certificate = X509Certificate::fromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml-sha1.crt');
        $this->assertEquals(XMLSecurityKey::RSA_SHA1, $certificate->getSignatureAlgorithm());
    }

    public function test_algorithm_sha256()
    {
        $certificate = X509Certificate::fromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml-sha256.crt');
        $this->assertEquals(XMLSecurityKey::RSA_SHA256, $certificate->getSignatureAlgorithm());
    }

    public function test_algorithm_sha384()
    {
        $certificate = X509Certificate::fromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml-sha384.crt');
        $this->assertEquals(XMLSecurityKey::RSA_SHA384, $certificate->getSignatureAlgorithm());
    }

    public function test_algorithm_sha512()
    {
        $certificate = X509Certificate::fromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml-sha512.crt');
        $this->assertEquals(XMLSecurityKey::RSA_SHA512, $certificate->getSignatureAlgorithm());
    }

    public function test_algorithm_md5()
    {
        $certificate = X509Certificate::fromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml-md5.crt');
        $this->assertEquals(SamlConstants::XMLDSIG_DIGEST_MD5, $certificate->getSignatureAlgorithm());
    }

    public function test_get_subject()
    {
        $certificate = new X509Certificate();
        $certificate->loadFromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml.crt');
        $this->assertEquals(
            array(
                'C' => 'RS',
                'ST' => 'Serbia',
                'O' => 'BOS',
                'CN' => 'mt.evo.team',
            ),
            $certificate->getSubject()
        );
    }

    public function test_get_issuer()
    {
        $certificate = new X509Certificate();
        $certificate->loadFromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml.crt');
        $this->assertEquals(
            array(
                'C' => 'RS',
                'ST' => 'Serbia',
                'O' => 'BOS',
                'CN' => 'mt.evo.team',
            ),
            $certificate->getIssuer()
        );
    }

    public function test_get_valid_from_timestamp()
    {
        $certificate = new X509Certificate();
        $certificate->loadFromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml.crt');
        $this->assertEquals(1381258772, $certificate->getValidFromTimestamp());
    }

    public function test_get_valid_to_timestamp()
    {
        $certificate = new X509Certificate();
        $certificate->loadFromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml.crt');
        $this->assertEquals(1696791572, $certificate->getValidToTimestamp());
    }

    public function test_get_fingerprint()
    {
        $certificate = new X509Certificate();
        $certificate->loadFromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml.crt');
        $this->assertEquals('9a092fb31216fd1a9af9427ffc98280bc30e2f81', $certificate->getFingerprint());
    }

    public function test_get_info()
    {
        $certificate = new X509Certificate();
        $certificate->loadFromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml.crt');
        $info = $certificate->getInfo();
        $this->assertArrayHasKey('name', $info);
        $this->assertArrayHasKey('subject', $info);
        $this->assertArrayHasKey('serialNumber', $info);
        $this->assertArrayHasKey('validFrom', $info);
        $this->assertArrayHasKey('validTo', $info);
        $this->assertArrayHasKey('validFrom_time_t', $info);
        $this->assertArrayHasKey('validTo_time_t', $info);
    }
}
