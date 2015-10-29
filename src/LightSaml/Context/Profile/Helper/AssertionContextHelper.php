<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Context\Profile\Helper;

use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Error\LightSamlContextException;
use LightSaml\Model\Assertion\EncryptedAssertionReader;

abstract class AssertionContextHelper
{
    /**
     * @param AssertionContext $context
     *
     * @return EncryptedAssertionReader
     */
    public static function getEncryptedAssertionReader(AssertionContext $context)
    {
        $result = $context->getEncryptedAssertion();
        if ($result instanceof EncryptedAssertionReader) {
            return $result;
        }

        throw new LightSamlContextException($context, 'Expected EncryptedAssertionReader');
    }
}
