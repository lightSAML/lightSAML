<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
