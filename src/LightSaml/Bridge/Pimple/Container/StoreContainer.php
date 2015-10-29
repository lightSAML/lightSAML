<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Bridge\Pimple\Container;

use LightSaml\Build\Container\StoreContainerInterface;
use LightSaml\Store\Id\IdStoreInterface;
use LightSaml\Store\Request\RequestStateStoreInterface;
use LightSaml\Store\Sso\SsoStateStoreInterface;

class StoreContainer extends AbstractPimpleContainer implements StoreContainerInterface
{
    const REQUEST_STATE_STORE = 'lightsaml.container.request_state_store';
    const ID_STATE_STORE = 'lightsaml.container.id_state_store';
    const SSO_STATE_STORE = 'lightsaml.container.sso_state_store';

    /**
     * @return RequestStateStoreInterface
     */
    public function getRequestStateStore()
    {
        return $this->pimple[self::REQUEST_STATE_STORE];
    }

    /**
     * @return IdStoreInterface
     */
    public function getIdStateStore()
    {
        return $this->pimple[self::ID_STATE_STORE];
    }

    /**
     * @return SsoStateStoreInterface
     */
    public function getSsoStateStore()
    {
        return $this->pimple[self::SSO_STATE_STORE];
    }
}
