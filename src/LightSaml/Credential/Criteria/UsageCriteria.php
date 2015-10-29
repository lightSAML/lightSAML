<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Credential\Criteria;

class UsageCriteria implements TrustCriteriaInterface
{
    /** @var string */
    protected $usage;

    /**
     * @param string $usage
     */
    public function __construct($usage)
    {
        $this->usage = $usage;
    }

    /**
     * @return string
     */
    public function getUsage()
    {
        return $this->usage;
    }
}
