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

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RequestStateSessionStore extends AbstractRequestStateArrayStore
{
    /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface */
    protected $session;

    /** @var string */
    protected $providerId;

    /** @var string */
    protected $prefix;

    /**
     * @param SessionInterface $session
     * @param string           $providerId
     * @param string           $prefix
     */
    public function __construct(SessionInterface $session, $providerId, $prefix = 'saml_request_state_')
    {
        $this->session = $session;
        $this->providerId = $providerId;
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    protected function getKey()
    {
        return sprintf('%s_%s', $this->providerId, $this->prefix);
    }

    /**
     * @return array
     */
    protected function getArray()
    {
        return $this->session->get($this->getKey(), array());
    }

    /**
     * @param array $arr
     *
     * @return AbstractRequestStateArrayStore
     */
    protected function setArray(array $arr)
    {
        $this->session->set($this->getKey(), $arr);
    }
}
