<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
