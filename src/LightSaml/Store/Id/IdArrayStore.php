<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Store\Id;

class IdArrayStore implements IdStoreInterface
{
    /** @var array */
    protected $store = array();

    /**
     * @param string    $entityId
     * @param string    $id
     * @param \DateTime $expiryTime
     *
     * @return void
     */
    public function set($entityId, $id, \DateTime $expiryTime)
    {
        if (false == isset($this->store[$entityId])) {
            $this->store[$entityId] = array();
        }
        $this->store[$entityId][$id] = $expiryTime;
    }

    /**
     * @param string $entityId
     * @param string $id
     *
     * @return bool
     */
    public function has($entityId, $id)
    {
        return isset($this->store[$entityId][$id]);
    }
}
