<?php

namespace LightSaml\Model\XmlDSig;

use LightSaml\Error\LightSamlSecurityException;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Security\KeyHelper;

class SignatureStringReader extends AbstractSignatureReader
{
    /** @var string */
    protected $signature;

    /** @var string */
    protected $algorithm;

    /** @var string */
    protected $data;

    /**
     * @param string|null $signature
     * @param string|null $algorithm
     * @param string|null $data
     */
    public function __construct($signature = null, $algorithm = null, $data = null)
    {
        $this->signature = $signature;
        $this->algorithm = $algorithm;
        $this->data = $data;
    }

    /**
     * @param string $algorithm
     */
    public function setAlgorithm($algorithm)
    {
        $this->algorithm = (string) $algorithm;
    }

    /**
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = (string) $data;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $signature
     */
    public function setSignature($signature)
    {
        $this->signature = (string) $signature;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param \XMLSecurityKey $key
     *
     * @return bool True if validated, False if validation was not performed
     *
     * @throws LightSamlSecurityException If validation fails
     */
    public function validate(\XMLSecurityKey $key)
    {
        if (null == $this->getSignature()) {
            return false;
        }

        if (\XMLSecurityKey::RSA_SHA1 != $key->type) {
            throw new LightSamlSecurityException('Key type must be RSA_SHA1 but got '.$key->type);
        }

        $key = $this->castKeyIfNecessary($key);

        $signature = base64_decode($this->getSignature());

        if (false == $key->verifySignature($this->getData(), $signature)) {
            throw new LightSamlSecurityException('Unable to validate signature on query string');
        }

        return true;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @throws \LogicException
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        throw new \LogicException('AbstractSignatureReader can not be serialized');
    }

    /**
     * @param \DOMElement            $node
     * @param DeserializationContext $context
     *
     * @throws \LogicException
     *
     * @return void
     */
    public function deserialize(\DOMElement $node, DeserializationContext $context)
    {
        throw new \LogicException('AbstractSignatureReader can not be serialized');
    }

    /**
     * @param \XMLSecurityKey $key
     *
     * @return \XMLSecurityKey
     */
    private function castKeyIfNecessary(\XMLSecurityKey $key)
    {
        $algorithm = $this->getAlgorithm();
        if ($key->type === \XMLSecurityKey::RSA_SHA1 && $algorithm !== $key->type) {
            $key = KeyHelper::castKey($key, $algorithm);
        }

        return $key;
    }
}
