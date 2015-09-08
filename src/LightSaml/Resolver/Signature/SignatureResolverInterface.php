<?php

namespace LightSaml\Resolver\Signature;

use LightSaml\Context\Profile\AbstractProfileContext;
use LightSaml\Model\XmlDSig\SignatureWriter;

interface SignatureResolverInterface
{
    /**
     * @param AbstractProfileContext $context
     *
     * @return SignatureWriter|null
     */
    public function getSignature(AbstractProfileContext $context);
}
