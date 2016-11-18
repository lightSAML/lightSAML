<?php

namespace LightSaml\Tests\Model\XmlDSig;

use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\XmlDSig\AbstractSignatureReader;
use LightSaml\Model\XmlDSig\SignatureStringReader;

class SignatureStringReaderTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_be_constructed_without_arguments()
    {
        new SignatureStringReader();
    }

    public function test_can_be_constructed_with_signature_algorithm_and_data()
    {
        new SignatureStringReader('signature', 'algo', 'data');
    }

    public function test_extends_abstract_signature_reader()
    {
        $reader = new SignatureStringReader();
        $this->assertInstanceOf(AbstractSignatureReader::class, $reader);
    }

    public function test_validate_returns_false_when_no_signature_set()
    {
        $publicKey = KeyHelper::createPublicKey(X509Certificate::fromFile(__DIR__ . '/../../../../../resources/sample/Certificate/saml.crt'));
        $reader = new SignatureStringReader();
        $result = $reader->validate($publicKey);
        $this->assertFalse($result);
    }

    public function test_validate_correct_signature()
    {
        $publicKey = KeyHelper::createPublicKey(X509Certificate::fromFile(__DIR__ . '/../../../../../resources/sample/Certificate/saml.crt'));
        $privateKey = KeyHelper::createPrivateKey(__DIR__ . '/../../../../../resources/sample/Certificate/saml.pem', '', true);
        $data = 'Some message data';
        $signature = base64_encode($privateKey->signData($data));

        $reader = new SignatureStringReader($signature, $publicKey->type, $data);
        $result = $reader->validate($publicKey);
        $this->assertTrue($result);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage SignatureStringReader can not be serialized
     */
    public function test_serialize_throws_exception()
    {
        $context = new SerializationContext();
        $reader = new SignatureStringReader();
        $reader->serialize($context->getDocument(), $context);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage SignatureStringReader can not be deserialized
     */
    public function test_deserialize_throws_exception()
    {
        $context = new DeserializationContext();
        $reader = new SignatureStringReader();
        $reader->deserialize($context->getDocument(), $context);
    }
}
