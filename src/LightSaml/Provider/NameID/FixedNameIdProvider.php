<?php

namespace LightSaml\Provider\NameID;

use LightSaml\Context\Profile\AbstractProfileContext;
use LightSaml\Model\Assertion\NameID;

class FixedNameIdProvider implements NameIdProviderInterface
{
    /** @var  NameID|null */
    protected $nameId;

    /**
     * @param NameID|null $nameId
     */
    public function __construct(NameID $nameId = null)
    {
        $this->nameId = $nameId;
    }

    /**
     * @param AbstractProfileContext $context
     *
     * @return NameID|null
     */
    public function getNameID(AbstractProfileContext $context)
    {
        return $this->nameId;
    }
}
