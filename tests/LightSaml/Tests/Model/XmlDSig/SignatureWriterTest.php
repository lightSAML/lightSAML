<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Model\XmlDSig;

use LightSaml\Meta\SigningOptions;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\Tests\BaseTestCase;

class SignatureWriterTest extends BaseTestCase
{
    public function test_create_with_signing_options()
    {
        SignatureWriter::create(new SigningOptions());
        $this->assertTrue(true);
    }

    public function test_create_with_key_and_certificate()
    {
        $writer = SignatureWriter::createByKeyAndCertificate(
            $this->getX509CertificateMock(),
            $this->getXmlSecurityKeyMock()
        );

        $this->assertNotNull($writer->getSigningOptions());
        $this->assertInstanceOf(SigningOptions::class, $writer->getSigningOptions());
    }

    public function test_constructs_with_certificate_and_key()
    {
        $writer = new SignatureWriter(
            $this->getX509CertificateMock(),
            $this->getXmlSecurityKeyMock()
        );

        $this->assertNull($writer->getSigningOptions());
    }

    public function test_can_be_constructed_wout_arguments()
    {
        new SignatureWriter();
        $this->assertTrue(true);
    }

    public function test_throws_logic_exception_on_deserialize()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('SignatureWriter can not be deserialized');

        $deserializationContext = new DeserializationContext();
        $deserializationContext->getDocument()->loadXML('<a></a>');
        $writer = new SignatureWriter();
        $writer->deserialize($deserializationContext->getDocument(), $deserializationContext);
    }

    public function test_returns_set_certificate()
    {
        $writer = new SignatureWriter();
        $writer->setCertificate($certificate = $this->getX509CertificateMock());
        $this->assertSame($certificate, $writer->getCertificate());
    }

    public function test_returns_set_key()
    {
        $writer = new SignatureWriter();
        $writer->setXmlSecurityKey($key = $this->getXmlSecurityKeyMock());
        $this->assertSame($key, $writer->getXmlSecurityKey());
    }
}
