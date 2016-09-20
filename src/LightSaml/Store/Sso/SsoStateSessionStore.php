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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SsoStateSessionStore implements SsoStateStoreInterface
{
    /** @var SessionInterface */
    protected $session;

    /** @var string */
    protected $key;

    /**
     * @param SessionInterface $session
     * @param string           $key
     */
    public function __construct(SessionInterface $session, $key)
    {
        $this->session = $session;
        $this->key = $key;
    }

    /**
     * @return SsoState
     */
    public function get()
    {
        $result = $this->session->get($this->key);
        if (null == $result) {
            $result = new SsoState();
            $this->set($result);
        }

        return $result;
    }

    /**
     * @param SsoState $ssoState
     *
     * @return void
     */
    public function set(SsoState $ssoState)
    {
        $ssoState->setLocalSessionId($this->session->getId());
        $this->session->set($this->key, $ssoState);
    }
}
