<?php

namespace LightSaml\Tests\Meta;

use LightSaml\Credential\X509Certificate;
use LightSaml\Meta\ParameterBag;
use LightSaml\Meta\SigningOptions;
use LightSaml\Tests\BaseTestCase;

class SigningOptionsTest extends BaseTestCase
{
    public function test_constructs_wout_arguments()
    {
        new SigningOptions();
        $this->assertTrue(true);
    }

    public function test_constructs_with_xml_key_and_certificate()
    {
        new SigningOptions($this->getXmlSecurityKeyMock(), new X509Certificate());
        $this->assertTrue(true);
    }

    public function test_enabled_by_default()
    {
        $options = new SigningOptions();
        $this->assertTrue($options->isEnabled());
    }

    public function test_can_be_disabled()
    {
        $options = new SigningOptions();
        $options->setEnabled(false);
        $this->assertFalse($options->isEnabled());
    }

    public function test_returns_certificate_constructed_with()
    {
        $options = new SigningOptions($key = $this->getXmlSecurityKeyMock(), $certificate = new X509Certificate());
        $this->assertSame($certificate, $options->getCertificate());
    }

    public function test_returns_xml_key_constructed_with()
    {
        $options = new SigningOptions($key = $this->getXmlSecurityKeyMock(), $certificate = new X509Certificate());
        $this->assertSame($key, $options->getPrivateKey());
    }

    public function test_returns_set_certificate()
    {
        $options = new SigningOptions();
        $options->setCertificate($certificate = new X509Certificate());
        $this->assertSame($certificate, $options->getCertificate());
    }

    public function test_returns_set_xml_key()
    {
        $options = new SigningOptions();
        $options->setPrivateKey($key = $this->getXmlSecurityKeyMock());
        $this->assertSame($key, $options->getPrivateKey());
    }

    public function test_returns_certificate_options()
    {
        $options = new SigningOptions();
        $this->assertNotNull($options->getCertificateOptions());
        $this->assertInstanceOf(ParameterBag::class, $options->getCertificateOptions());
    }
}
