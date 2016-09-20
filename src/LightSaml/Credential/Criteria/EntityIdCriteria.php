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

class EntityIdCriteria implements TrustCriteriaInterface
{
    /** @var string */
    protected $entityId;

    /**
     * @param string $entityId
     */
    public function __construct($entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * @return string
     */
    public function getEntityId()
    {
        return $this->entityId;
    }
}
