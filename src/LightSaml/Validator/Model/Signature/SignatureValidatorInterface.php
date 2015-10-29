<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
