<?php

namespace LightSaml\Tests\Model\XmlDSig;

use LightSaml\Meta\SigningOptions;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\Tests\TestHelper;

class SignatureWriterTest extends \PHPUnit_Framework_TestCase
{
    public function test_create_with_signing_options()
    {
        SignatureWriter::create(new SigningOptions());
    }

    public function test_create_with_key_and_certificate()
    {
        $writer = SignatureWriter::createByKeyAndCertificate(
            TestHelper::getX509CertificateMock($this),
            TestHelper::getXmlSecurityKeyMock($this)
        );

        $this->assertNotNull($writer->getSigningOptions());
        $this->assertInstanceOf(SigningOptions::class, $writer->getSigningOptions());
    }

    public function test_constructs_with_certificate_and_key()
    {
        $writer = new SignatureWriter(
            TestHelper::getX509CertificateMock($this),
            TestHelper::getXmlSecurityKeyMock($this)
        );

        $this->assertNull($writer->getSigningOptions());
    }

    public function test_can_be_constructed_wout_arguments()
    {
        new SignatureWriter();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage SignatureWriter can not be deserialized
     */
    public function test_throws_logic_exception_on_deserialize()
    {
        $deserializationContext = new DeserializationContext();
        $deserializationContext->getDocument()->loadXML('<a></a>');
        $writer = new SignatureWriter();
        $writer->deserialize($deserializationContext->getDocument(), $deserializationContext);
    }

    public function test_returns_set_certificate()
    {
        $writer = new SignatureWriter();
        $writer->setCertificate($certificate = TestHelper::getX509CertificateMock($this));
        $this->assertSame($certificate, $writer->getCertificate());
    }

    public function test_returns_set_key()
    {
        $writer = new SignatureWriter();
        $writer->setXmlSecurityKey($key = TestHelper::getXmlSecurityKeyMock($this));
        $this->assertSame($key, $writer->getXmlSecurityKey());
    }
}
