<?php

namespace LightSaml\Provider\Attribute;

use LightSaml\Model\Assertion\Attribute;

interface AttributeNameProviderInterface
{
    /**
     * @return Attribute[]
     */
    public function getAttributes();
}
