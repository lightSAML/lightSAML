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

use LightSaml\Meta\SigningOptions;
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

    /** @var SigningOptions */
    protected $signingOptions;

    /**
     * @param SigningOptions $options
     *
     * @return SignatureWriter
     */
    public static function create(SigningOptions $options)
    {
        $writer = new self($options->getCertificate(), $options->getPrivateKey());
        $writer->signingOptions = $options;

        return $writer;
    }

    /**
     * @param X509Certificate $certificate
     * @param XMLSecurityKey  $xmlSecurityKey
     *
     * @return SignatureWriter
     */
    public static function createByKeyAndCertificate(X509Certificate $certificate, XMLSecurityKey $xmlSecurityKey)
    {
        $signingOptions = new SigningOptions($xmlSecurityKey, $certificate);

        return self::create($signingOptions);
    }

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
     * @return SigningOptions
     */
    public function getSigningOptions()
    {
        return $this->signingOptions;
    }

    /**
     * @param SigningOptions $signingOptions
     *
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
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        if ($this->signingOptions && false === $this->signingOptions->isEnabled()) {
            return;
        }

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

        $objXMLSecDSig->add509Cert(
            $this->getCertificate()->getData(),
            false,
            false,
            $this->signingOptions ? $this->signingOptions->getCertificateOptions()->all() : null
        );

        $firstChild = $parent->hasChildNodes() ? $parent->firstChild : null;
        if ($firstChild && $firstChild->localName == 'Issuer') {
            // The signature node should come after the issuer node
            $firstChild = $firstChild->nextSibling;
        }
        $objXMLSecDSig->insertSignature($parent, $firstChild);
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        throw new \LogicException('SignatureWriter can not be deserialized');
    }
}
