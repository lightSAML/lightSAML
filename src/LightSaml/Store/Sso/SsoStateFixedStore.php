<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Store\Sso;

use LightSaml\State\Sso\SsoState;

class SsoStateFixedStore implements SsoStateStoreInterface
{
    /** @var SsoState */
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
