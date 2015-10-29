<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Store\Request;

use LightSaml\State\Request\RequestState;

interface RequestStateStoreInterface
{
    /**
     * @param RequestState $state
     *
     * @return RequestStateStoreInterface
     */
    public function set(RequestState $state);

    /**
     * @param string $id
     *
     * @return RequestState|null
     */
    public function get($id);

    /**
     * @param string $id
     *
     * @return bool
     */
    public function remove($id);

    /**
     * @return void
     */
    public function clear();
}
