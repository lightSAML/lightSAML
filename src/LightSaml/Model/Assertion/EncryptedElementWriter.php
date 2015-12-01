<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Model\Assertion;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Error\LightSamlException;
use LightSaml\Model\AbstractSamlModel;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecEnc;

abstract class EncryptedElementWriter extends EncryptedElement
{
    /** @var \DOMElement */
    protected $encryptedElement;

    /**
     * @param AbstractSamlModel $object
     * @param XMLSecurityKey    $key
     *
     * @return SerializationContext
     */
    public function encrypt(AbstractSamlModel $object, XMLSecurityKey $key)
    {
        $serializationContext = new SerializationContext();
        $object->serialize($serializationContext->getDocument(), $serializationContext);

        $enc = new XMLSecEnc();
        $enc->setNode($serializationContext->getDocument()->firstChild);
        $enc->type = XMLSecEnc::Element;

        switch ($key->type) {
            case XMLSecurityKey::TRIPLEDES_CBC:
            case XMLSecurityKey::AES128_CBC:
            case XMLSecurityKey::AES192_CBC:
            case XMLSecurityKey::AES256_CBC:
                $symmetricKey = $key;
                break;

            case XMLSecurityKey::RSA_1_5:
            case XMLSecurityKey::RSA_SHA1:
            case XMLSecurityKey::RSA_SHA256:
            case XMLSecurityKey::RSA_SHA384:
            case XMLSecurityKey::RSA_SHA512:
            case XMLSecurityKey::RSA_OAEP_MGF1P:
                $symmetricKey = new XMLSecurityKey(XMLSecurityKey::AES128_CBC);
                $symmetricKey->generateSessionKey();

                $enc->encryptKey($key, $symmetricKey);

                break;

            default:
                throw new LightSamlException(sprintf('Unknown key type for encryption: "%s"', $key->type));
        }

        $this->encryptedElement = $enc->encryptNode($symmetricKey);

        return $serializationContext;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return \DOMElement
     */
    abstract protected function createRootElement(\DOMNode $parent, SerializationContext $context);

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        if (null === $this->encryptedElement) {
            throw new LightSamlException('Encrypted element missing');
        }

        $root = $this->createRootElement($parent, $context);

        $root->appendChild($context->getDocument()->importNode($this->encryptedElement, true));
    }

    /**
     * @param \DOMElement            $node
     * @param DeserializationContext $context
     *
     * @return void
     */
    public function deserialize(\DOMElement $node, DeserializationContext $context)
    {
        throw new \LogicException('EncryptedElementWriter can not be used for deserialization');
    }
}
