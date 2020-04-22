<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Model\Xsd;

use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Metadata\EntitiesDescriptor;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Protocol\SamlMessage;
use LightSaml\Model\SamlElementInterface;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\Tests\BaseTestCase;
use LightSaml\Validator\Model\Xsd\XsdValidator;

abstract class AbstractXsdValidationTest extends BaseTestCase
{
    protected function setUp(): void
    {
        libxml_use_internal_errors(true);
    }

    /**
     * @return X509Certificate
     */
    protected function getX509Certificate()
    {
        return X509Certificate::fromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml.crt');
    }

    /**
     * @param SamlMessage|EntityDescriptor|EntitiesDescriptor|Assertion $object
     */
    protected function sign($object)
    {
        $object->setSignature(new SignatureWriter(
            $this->getX509Certificate(),
            KeyHelper::createPrivateKey(__DIR__.'/../../../../../resources/sample/Certificate/saml.pem', '', true)
        ));
    }

    protected function validateProtocol(SamlElementInterface $samlElement)
    {
        $validator = new XsdValidator();
        $xml = $this->serialize($samlElement);
        $errors = $validator->validateProtocol($xml);
        if ($errors) {
            $this->fail("\n".implode("\n", $errors)."\n\n$xml\n\n");
        }
        $this->assertTrue(true);
    }

    protected function validateMetadata(SamlElementInterface $samlElement)
    {
        $validator = new XsdValidator();
        $xml = $this->serialize($samlElement);
        $errors = $validator->validateMetadata($xml);
        if ($errors) {
            $this->fail("\n".implode("\n", $errors)."\n\n$xml\n\n");
        }
        $this->assertTrue(true);
    }

    /**
     * @return string
     */
    private function serialize(SamlElementInterface $samlElement)
    {
        $serializationContext = new SerializationContext();
        $samlElement->serialize($serializationContext->getDocument(), $serializationContext);

        return $serializationContext->getDocument()->saveXML();
    }
}
