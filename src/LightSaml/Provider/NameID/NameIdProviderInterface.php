<?php

namespace LightSaml\Provider\NameID;

use LightSaml\Context\Profile\AbstractProfileContext;
use LightSaml\Model\Assertion\NameID;

interface NameIdProviderInterface
{
    /**
     * @param AbstractProfileContext $context
     *
     * @return NameID|null
     */
    public function getNameID(AbstractProfileContext $context);
}
