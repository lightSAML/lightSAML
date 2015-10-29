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

class FallbackTrustOptionsStore implements TrustOptionsStoreInterface
{
    /** @var TrustOptionsStoreInterface */
    private $first;

    /** @var TrustOptionsStoreInterface */
    private $second;

    /**
     * @param TrustOptionsStoreInterface $first
     * @param TrustOptionsStoreInterface $second
     */
    public function __construct(TrustOptionsStoreInterface $first = null, TrustOptionsStoreInterface $second = null)
    {
        $this->first = $first;
        $this->second = $second;
    }

    /**
     * @return TrustOptionsStoreInterface|null
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * @param TrustOptionsStoreInterface|null $first
     *
     * @return FallbackTrustOptionsStore
     */
    public function setFirst(TrustOptionsStoreInterface $first = null)
    {
        $this->first = $first;

        return $this;
    }

    /**
     * @return TrustOptionsStoreInterface|null
     */
    public function getSecond()
    {
        return $this->second;
    }

    /**
     * @param TrustOptionsStoreInterface|null $second
     *
     * @return FallbackTrustOptionsStore
     */
    public function setSecond(TrustOptionsStoreInterface $second = null)
    {
        $this->second = $second;

        return $this;
    }

    /**
     * @param string $entityId
     *
     * @return TrustOptions|null
     */
    public function get($entityId)
    {
        if ($this->first && $this->first->has($entityId)) {
            return $this->first->get($entityId);
        }
        if ($this->second && $this->second->has($entityId)) {
            return $this->second->get($entityId);
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
        if ($this->first && $this->first->has($entityId)) {
            return true;
        }
        if ($this->second && $this->second->has($entityId)) {
            return true;
        }

        return false;
    }
}
