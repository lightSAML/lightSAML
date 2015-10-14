<?php

namespace LightSaml\Validator\Model\Signature;

use LightSaml\Credential\CredentialInterface;
use LightSaml\Model\XmlDSig\AbstractSignatureReader;

interface SignatureValidatorInterface
{
    /**
     * @param AbstractSignatureReader $signature
     * @param string                  $issuer
     * @param string                  $metadataType
     *
     * @return CredentialInterface|null
     */
    public function validate(AbstractSignatureReader $signature, $issuer, $metadataType);
}
