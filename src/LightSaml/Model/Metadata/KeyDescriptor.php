<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Model\Metadata;

use LightSaml\Error\LightSamlXmlException;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\AbstractSamlModel;
use LightSaml\SamlConstants;
use LightSaml\Credential\X509Certificate;

class KeyDescriptor extends AbstractSamlModel
{
    const USE_SIGNING = 'signing';
    const USE_ENCRYPTION = 'encryption';

    /** @var string */
    protected $use;

    /** @var X509Certificate */
    private $certificate;

    /**
     * @param string|null          $use
     * @param X509Certificate|null $certificate
     */
    public function __construct($use = null, X509Certificate $certificate = null)
    {
        $this->use = $use;
        $this->certificate = $certificate;
    }

    /**
     * @param string $use
     *
     * @return KeyDescriptor
     *
     * @throws \InvalidArgumentException
     */
    public function setUse($use)
    {
        $use = trim($use);
        if (false != $use && self::USE_ENCRYPTION != $use && self::USE_SIGNING != $use) {
            throw new \InvalidArgumentException(sprintf("Invalid use value '%s'", $use));
        }
        $this->use = $use;

        return $this;
    }

    /**
     * @return string
     */
    public function getUse()
    {
        return $this->use;
    }

    /**
     * @param X509Certificate $certificate
     *
     * @return KeyDescriptor
     */
    public function setCertificate(X509Certificate $certificate)
    {
        $this->certificate = $certificate;

        return $this;
    }

    /**
     * @return X509Certificate
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('KeyDescriptor', SamlConstants::NS_METADATA, $parent, $context);

        $this->attributesToXml(array('use'), $result);

        $keyInfo = $this->createElement('ds:KeyInfo', SamlConstants::NS_XMLDSIG, $result, $context);
        $xData = $this->createElement('ds:X509Data', SamlConstants::NS_XMLDSIG, $keyInfo, $context);
        $xCert = $this->createElement('ds:X509Certificate', SamlConstants::NS_XMLDSIG, $xData, $context);

        $xCert->nodeValue = $this->getCertificate()->getData();
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'KeyDescriptor', SamlConstants::NS_METADATA);

        $this->attributesFromXml($node, array('use'));

        $list = $context->getXpath()->query('./ds:KeyInfo/ds:X509Data/ds:X509Certificate', $node);
        if (1 != $list->length) {
            throw new LightSamlXmlException('Missing X509Certificate node');
        }

        /** @var $x509CertificateNode \DOMElement */
        $x509CertificateNode = $list->item(0);
        $certificateData = trim($x509CertificateNode->textContent);
        if (false == $certificateData) {
            throw new LightSamlXmlException('Missing certificate data');
        }

        $this->certificate = new X509Certificate();
        $this->certificate->setData($certificateData);
    }
}
