<?php

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
