<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Store\TrustOptions;

use LightSaml\Meta\TrustOptions\TrustOptions;

class StaticTrustOptionsStore implements TrustOptionsStoreInterface
{
    /** @var TrustOptions[] */
    protected $options = array();

    /**
     * @param string       $entityId
     * @param TrustOptions $options
     *
     * @return StaticTrustOptionsStore
     */
    public function add($entityId, TrustOptions $options)
    {
        $this->options[$entityId] = $options;

        return $this;
    }

    /**
     * @param string $entityId
     *
     * @return TrustOptions|null
     */
    public function get($entityId)
    {
        return isset($this->options[$entityId]) ? $this->options[$entityId] : null;
    }

    /**
     * @param string $entityId
     *
     * @return bool
     */
    public function has($entityId)
    {
        return isset($this->options[$entityId]);
    }
}
