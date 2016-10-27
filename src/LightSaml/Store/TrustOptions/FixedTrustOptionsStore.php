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

class FixedTrustOptionsStore implements TrustOptionsStoreInterface
{
    /** @var TrustOptions */
    protected $option;

    /**
     * @param TrustOptions $option
     */
    public function __construct(TrustOptions $option = null)
    {
        $this->option = $option;
    }

    /**
     * @param TrustOptions|null $trustOptions
     *
     * @return FixedTrustOptionsStore
     */
    public function setTrustOptions(TrustOptions $trustOptions = null)
    {
        $this->option = $trustOptions;

        return $this;
    }

    /**
     * @param string $entityId
     *
     * @return TrustOptions|null
     */
    public function get($entityId)
    {
        return $this->option;
    }

    /**
     * @param string $entityId
     *
     * @return bool
     */
    public function has($entityId)
    {
        return true;
    }
}
