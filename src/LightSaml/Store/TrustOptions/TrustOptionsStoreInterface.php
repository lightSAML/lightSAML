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

interface TrustOptionsStoreInterface
{
    /**
     * @param string $entityId
     *
     * @return TrustOptions|null
     */
    public function get($entityId);

    /**
     * @param string $entityId
     *
     * @return bool
     */
    public function has($entityId);
}
