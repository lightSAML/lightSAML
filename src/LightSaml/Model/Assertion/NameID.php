<?php

namespace LightSaml\Model\Assertion;

class NameID extends AbstractNameID
{
    /**
     * @return string
     */
    protected function getElementName()
    {
        return 'NameID';
    }
}
