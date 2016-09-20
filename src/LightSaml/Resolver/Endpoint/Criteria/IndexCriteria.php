<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Resolver\Endpoint\Criteria;

use LightSaml\Criteria\CriteriaInterface;

class IndexCriteria implements CriteriaInterface
{
    /** @var string */
    protected $index;

    /**
     * @param string $index
     */
    public function __construct($index)
    {
        $this->index = $index;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }
}
