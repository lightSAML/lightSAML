<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Resolver\Session;

use LightSaml\Model\Assertion\Assertion;

interface SessionProcessorInterface
{
    /**
     * @param Assertion[] $assertions
     * @param string      $ownEntityId
     * @param string      $partyEntityId
     */
    public function processAssertions(array $assertions, $ownEntityId, $partyEntityId);
}
