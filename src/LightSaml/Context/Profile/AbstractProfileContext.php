<?php

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
