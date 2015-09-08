<?php

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
