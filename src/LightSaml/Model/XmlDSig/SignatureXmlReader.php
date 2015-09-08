<?php

namespace LightSaml\Model\XmlDSig;

use LightSaml\Error\LightSamlSecurityException;
use LightSaml\Error\LightSamlXmlException;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;
use LightSaml\Model\Security\KeyHelper;

class SignatureXmlReader extends AbstractSignatureReader
{
    /** @var \XMLSecurityDSig */
    protected $signature;

    /** @var string[] */
    protected $certificates = array();

    /**
     * @param string $certificate
     */
    public function addCertificate($certificate)
    {
        $this->certificates[] = (string) $certificate;
    }

    /**
     * @return \string[]
     */
    public function getAllCertificates()
    {
        return $this->certificates;
    }

    /**
     * @param \XMLSecurityDSig $signature
     */
    public function setSignature(\XMLSecurityDSig $signature)
    {
        $this->signature = $signature;
    }

    /**
     * @return \XMLSecurityDSig
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param \XMLSecurityKey $key
     *
     * @return bool
     *
     * @throws LightSamlSecurityException
     */
    public function validate(\XMLSecurityKey $key)
    {
        if (null == $this->signature) {
            return false;
        }

        if (false == $this->signature->validateReference()) {
            throw new LightSamlSecurityException('Digest validation failed');
        }

        if (\XMLSecurityKey::RSA_SHA1 != $key->type) {
            throw new LightSamlSecurityException('Key type must be RSA_SHA1 but got '.$key->type);
        }

        $key = $this->castKeyIfNecessary($key);

        if (false == $this->signature->verify($key)) {
            throw new LightSamlSecurityException('Unable to verify Signature');
        }

        return true;
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

    /**
     * @return string
     * @throws \LightSaml\Error\LightSamlXmlException
     */
    private function getAlgorithm()
    {
        $xpath = new \DOMXPath(
            $this->signature->sigNode instanceof \DOMDocument
            ? $this->signature->sigNode
            : $this->signature->sigNode->ownerDocument
        );
        $xpath->registerNamespace('ds', \XMLSecurityDSig::XMLDSIGNS);

        $list = $xpath->query('./ds:SignedInfo/ds:SignatureMethod', $this->signature->sigNode);
        if (!$list || $list->length == 0) {
            throw new LightSamlXmlException('Missing SignatureMethod element');
        }
        /** @var $sigMethod \DOMElement */
        $sigMethod = $list->item(0);
        if (!$sigMethod->hasAttribute('Algorithm')) {
            throw new LightSamlXmlException('Missing Algorithm-attribute on SignatureMethod element.');
        }
        $algorithm = $sigMethod->getAttribute('Algorithm');

        return $algorithm;
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
        throw new \LogicException('SignatureXmlReader can not be serialized');
    }

    /**
     * @param \DOMElement            $node
     * @param DeserializationContext $context
     *
     * @throws \LightSaml\Error\LightSamlSecurityException
     *
     * @return void
     */
    public function deserialize(\DOMElement $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'Signature', SamlConstants::NS_XMLDSIG);

        $this->signature = new \XMLSecurityDSig();
        $this->signature->idKeys[] = $this->getIDName();
        $this->signature->sigNode = $node;
        $this->signature->canonicalizeSignedInfo();

        $this->key = null;
        $key = new \XMLSecurityKey(\XMLSecurityKey::RSA_SHA1, array('type' => 'public'));
        \XMLSecEnc::staticLocateKeyInfo($key, $node);
        if ($key->name || $key->key) {
            $this->key = $key;
        }

        $this->certificates = array();
        $list = $context->getXpath()->query('./ds:KeyInfo/ds:X509Data/ds:X509Certificate', $node);
        foreach ($list as $certNode) {
            $certData = trim($certNode->textContent);
            $certData = str_replace(array("\r", "\n", "\t", ' '), '', $certData);
            $this->certificates[] = $certData;
        }
    }
}
