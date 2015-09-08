<?php

namespace LightSaml\Store\Sso;

use LightSaml\State\Sso\SsoState;

class SsoStateFixedStore implements SsoStateStoreInterface
{
    /** @var  SsoState */
    protected $ssoState;

    /**
     * @return SsoState
     */
    public function get()
    {
        if (null == $this->ssoState) {
            $this->ssoState = new SsoState();
        }

        return $this->ssoState;
    }

    /**
     * @param SsoState $ssoState
     *
     * @return void
     */
    public function set(SsoState $ssoState)
    {
        $this->ssoState = $ssoState;
    }
}
