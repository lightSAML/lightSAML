<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Model\Assertion;

class Issuer extends AbstractNameID
{
    /**
     * @return string
     */
    protected function getElementName()
    {
        return 'Issuer';
    }
}
