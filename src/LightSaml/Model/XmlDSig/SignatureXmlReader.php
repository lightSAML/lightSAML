<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Model\XmlDSig;

use LightSaml\Error\LightSamlSecurityException;
use LightSaml\Error\LightSamlXmlException;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecEnc;

class SignatureXmlReader extends AbstractSignatureReader
{
    /** @var XMLSecurityDSig */
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
     * @param XMLSecurityDSig $signature
     */
    public function setSignature(XMLSecurityDSig $signature)
    {
        $this->signature = $signature;
    }

    /**
     * @return XMLSecurityDSig
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param XMLSecurityKey $key
     *
     * @return bool
     *
     * @throws LightSamlSecurityException
     */
    public function validate(XMLSecurityKey $key)
    {
        if (null == $this->signature) {
            return false;
        }

        if (false == $this->signature->validateReference()) {
            throw new LightSamlSecurityException('Digest validation failed');
        }

        $key = $this->castKeyIfNecessary($key);

        if (false == $this->signature->verify($key)) {
            throw new LightSamlSecurityException('Unable to verify Signature');
        }

        return true;
    }

    /**
     * @return string
     *
     * @throws \LightSaml\Error\LightSamlXmlException
     */
    public function getAlgorithm()
    {
        $xpath = new \DOMXPath(
            $this->signature->sigNode instanceof \DOMDocument
            ? $this->signature->sigNode
            : $this->signature->sigNode->ownerDocument
        );
        $xpath->registerNamespace('ds', XMLSecurityDSig::XMLDSIGNS);

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
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        throw new \LogicException('SignatureXmlReader can not be serialized');
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     *
     * @throws \Exception
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'Signature', SamlConstants::NS_XMLDSIG);

        $this->signature = new XMLSecurityDSig();
        $this->signature->idKeys[] = $this->getIDName();
        $this->signature->sigNode = $node;
        $this->signature->canonicalizeSignedInfo();

        $this->key = null;
        $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type' => 'public'));
        XMLSecEnc::staticLocateKeyInfo($key, $node);
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
