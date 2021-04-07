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

use LightSaml\Credential\X509Certificate;
use LightSaml\Meta\SigningOptions;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class SignatureWriter extends Signature
{
    /** @var string */
    protected $canonicalMethod = XMLSecurityDSig::EXC_C14N;

    /** @var XMLSecurityKey */
    protected $xmlSecurityKey;

    /** @var X509Certificate */
    protected $certificate;

    protected $digestAlgorithm = XMLSecurityDSig::SHA1;

    /** @var SigningOptions */
    protected $signingOptions;

    /**
     * @return SignatureWriter
     */
    public static function create(SigningOptions $options)
    {
        $writer = new self($options->getCertificate(), $options->getPrivateKey());
        $writer->signingOptions = $options;

        return $writer;
    }

    /**
     * @return SignatureWriter
     */
    public static function createByKeyAndCertificate(X509Certificate $certificate, XMLSecurityKey $xmlSecurityKey)
    {
        $signingOptions = new SigningOptions($xmlSecurityKey, $certificate);

        return self::create($signingOptions);
    }

    /**
     * @param string $digestAlgorithm
     */
    public function __construct(X509Certificate $certificate = null, XMLSecurityKey $xmlSecurityKey = null, $digestAlgorithm = XMLSecurityDSig::SHA1)
    {
        $this->certificate = $certificate;
        $this->xmlSecurityKey = $xmlSecurityKey;
        $this->digestAlgorithm = $digestAlgorithm;
    }

    /**
     * @return string
     */
    public function getDigestAlgorithm()
    {
        return $this->digestAlgorithm;
    }

    /**
     * @param string $digestAlgorithm
     *
     * @return SignatureWriter
     */
    public function setDigestAlgorithm($digestAlgorithm)
    {
        $this->digestAlgorithm = $digestAlgorithm;

        return $this;
    }

    /**
     * @return SigningOptions
     */
    public function getSigningOptions()
    {
        return $this->signingOptions;
    }

    /**
     * @return SignatureWriter
     */
    public function setSigningOptions(SigningOptions $signingOptions)
    {
        $this->signingOptions = $signingOptions;

        return $this;
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

    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        if ($this->signingOptions && false === $this->signingOptions->isEnabled()) {
            return;
        }

        $objXMLSecDSig = new XMLSecurityDSig();
        $objXMLSecDSig->setCanonicalMethod($this->getCanonicalMethod());
        $key = $this->getXmlSecurityKey();

        $objXMLSecDSig->addReferenceList(
            [$parent],
            $this->digestAlgorithm,
            [SamlConstants::XMLSEC_TRANSFORM_ALGORITHM_ENVELOPED_SIGNATURE, XMLSecurityDSig::EXC_C14N],
            ['id_name' => $this->getIDName(), 'overwrite' => false]
        );

        $objXMLSecDSig->sign($key);

        $objXMLSecDSig->add509Cert(
            $this->getCertificate()->getData(),
            false,
            false,
            $this->signingOptions ? $this->signingOptions->getCertificateOptions()->all() : null
        );

        $firstChild = $parent->hasChildNodes() ? $parent->firstChild : null;
        if ($firstChild && 'Issuer' == $firstChild->localName) {
            // The signature node should come after the issuer node
            $firstChild = $firstChild->nextSibling;
        }
        $objXMLSecDSig->insertSignature($parent, $firstChild);
    }

    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        throw new \LogicException('SignatureWriter can not be deserialized');
    }
}
