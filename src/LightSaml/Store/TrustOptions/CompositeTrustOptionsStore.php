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

class CompositeTrustOptionsStore implements TrustOptionsStoreInterface
{
    /** @var TrustOptionsStoreInterface[] */
    private $children = [];

    /**
     * @param TrustOptionsStoreInterface[] $stores
     */
    public function __construct(array $stores = array())
    {
        foreach ($stores as $store) {
            $this->add($store);
        }
    }

    /**
     * @param TrustOptionsStoreInterface $store
     *
     * @return CompositeTrustOptionsStore This instance
     */
    public function add(TrustOptionsStoreInterface $store)
    {
        $this->children[] = $store;

        return $this;
    }

    /**
     * @param string $entityId
     *
     * @return TrustOptions|null
     */
    public function get($entityId)
    {
        foreach ($this->children as $store) {
            $result = $store->get($entityId);
            if ($result) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @param string $entityId
     *
     * @return bool
     */
    public function has($entityId)
    {
        foreach ($this->children as $store) {
            if ($store->has($entityId)) {
                return true;
            }
        }

        return false;
    }
}
