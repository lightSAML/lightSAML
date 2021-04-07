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

interface IdStoreInterface
{
    /**
     * @param string $entityId
     * @param string $id
     *
     * @return void
     */
    public function set($entityId, $id, \DateTime $expiryTime);

    /**
     * @param string $entityId
     * @param string $id
     *
     * @return bool
     */
    public function has($entityId, $id);
}
