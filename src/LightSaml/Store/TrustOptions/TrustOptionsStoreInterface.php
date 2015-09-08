<?php

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
