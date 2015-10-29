<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Context\Profile;

use LightSaml\Context\AbstractContext;
use LightSaml\Error\LightSamlContextException;

abstract class AbstractProfileContext extends AbstractContext
{
    /**
     * @return ProfileContext
     */
    public function getProfileContext()
    {
        $result = $this;
        while ($result && false == $result instanceof ProfileContext) {
            $result = $result->getParent();
        }

        if ($result) {
            return $result;
        }

        throw new LightSamlContextException($this, 'Missing ProfileContext');
    }
}
