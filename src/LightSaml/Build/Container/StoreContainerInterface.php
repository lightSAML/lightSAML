<?php

namespace LightSaml\Build\Container;

use LightSaml\Store\Id\IdStoreInterface;
use LightSaml\Store\Request\RequestStateStoreInterface;
use LightSaml\Store\Sso\SsoStateStoreInterface;

interface StoreContainerInterface
{
    /**
     * @return RequestStateStoreInterface
     */
    public function getRequestStateStore();

    /**
     * @return IdStoreInterface
     */
    public function getIdStateStore();

    /**
     * @return SsoStateStoreInterface
     */
    public function getSsoStateStore();
}
