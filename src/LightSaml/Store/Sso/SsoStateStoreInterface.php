<?php

namespace LightSaml\Store\Sso;

use LightSaml\State\Sso\SsoState;

interface SsoStateStoreInterface
{
    /**
     * @return SsoState
     */
    public function get();

    /**
     * @param SsoState $ssoState
     *
     * @return void
     */
    public function set(SsoState $ssoState);
}
