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

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;
use LightSaml\Credential\X509Certificate;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecurityDSig;

class SignatureWriter extends Signature
{
    /** @var string */
    protected $canonicalMethod = XMLSecurityDSig::EXC_C14N;

    /** @var XMLSecurityKey */
    protected $xmlSecurityKey;

    /** @var X509Certificate */
    protected $certificate;

    /**
     * @param X509Certificate|null $certificate
     * @param XMLSecurityKey|null  $xmlSecurityKey
     */
    public function __construct(X509Certificate $certificate = null, XMLSecurityKey $xmlSecurityKey = null)
    {
        $this->certificate = $certificate;
        $this->xmlSecurityKey = $xmlSecurityKey;
    }

    /**
     * @return string
     */
    public function getCanonicalMethod()
    {
        return $this->canonicalMethod;
    }

    /**
     * @param string $canonicalMethod
     *
     * @return SignatureWriter
     */
    public function setCanonicalMethod($canonicalMethod)
    {
        $this->canonicalMethod = $canonicalMethod;

        return $this;
    }

    /**
     * @param XMLSecurityKey $key
     *
     * @return SignatureWriter
     */
    public function setXmlSecurityKey(XMLSecurityKey $key)
    {
        $this->xmlSecurityKey = $key;

        return $this;
    }

    /**
     * @return XMLSecurityKey
     */
    public function getXmlSecurityKey()
    {
        return $this->xmlSecurityKey;
    }

    /**
     * @param X509Certificate $certificate
     *
     * @return SignatureWriter
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
        $objXMLSecDSig = new XMLSecurityDSig();
        $objXMLSecDSig->setCanonicalMethod($this->getCanonicalMethod());
        $key = $this->getXmlSecurityKey();
        switch ($key->type) {
            case XMLSecurityKey::RSA_SHA256:
                $type = XMLSecurityDSig::SHA256;
                break;
            case XMLSecurityKey::RSA_SHA384:
                $type = XMLSecurityDSig::SHA384;
                break;
            case XMLSecurityKey::RSA_SHA512:
                $type = XMLSecurityDSig::SHA512;
                break;
            default:
                $type = XMLSecurityDSig::SHA1;
        }

        $objXMLSecDSig->addReferenceList(
            array($parent),
            $type,
            array(SamlConstants::XMLSEC_TRANSFORM_ALGORITHM_ENVELOPED_SIGNATURE, XMLSecurityDSig::EXC_C14N),
            array('id_name' => $this->getIDName(), 'overwrite' => false)
        );

        $objXMLSecDSig->sign($key);
        $objXMLSecDSig->add509Cert($this->getCertificate()->getData(), false, false, array('subjectName' => false));
        $firstChild = $parent->hasChildNodes() ? $parent->firstChild : null;
        if ($firstChild && $firstChild->localName == 'Issuer') {
            // The signature node should come after the issuer node
            $firstChild = $firstChild->nextSibling;
        }
        $objXMLSecDSig->insertSignature($parent, $firstChild);
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
        throw new \LogicException('SignatureWriter can not be deserialize');
    }
}
