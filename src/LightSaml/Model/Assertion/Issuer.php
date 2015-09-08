<?php

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
