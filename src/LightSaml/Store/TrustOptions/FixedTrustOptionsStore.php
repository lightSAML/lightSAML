<?php

namespace LightSaml\Store\TrustOptions;

use LightSaml\Meta\TrustOptions\TrustOptions;

class FixedTrustOptionsStore implements TrustOptionsStoreInterface
{
    /** @var TrustOptions */
    protected $option;

    /**
     * @param TrustOptions $option
     */
    public function __construct(TrustOptions $option)
    {
        $this->option = $option;
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
