<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Provider\NameID;

use LightSaml\Context\Profile\AbstractProfileContext;
use LightSaml\Model\Assertion\NameID;

class FixedNameIdProvider implements NameIdProviderInterface
{
    /** @var NameID|null */
    protected $nameId;

    /**
     * @param NameID|null $nameId
     */
    public function __construct(NameID $nameId = null)
    {
        $this->nameId = $nameId;
    }

    /**
     * @param NameID|null $nameId
     *
     * @return FixedNameIdProvider
     */
    public function setNameId(NameID $nameId = null)
    {
        $this->nameId = $nameId;

        return $this;
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
