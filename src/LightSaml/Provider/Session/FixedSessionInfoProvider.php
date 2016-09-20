<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Provider\Session;

class FixedSessionInfoProvider implements SessionInfoProviderInterface
{
    /** @var int */
    protected $authnInstant;

    /** @var string */
    protected $sessionIndex;

    /** @var string */
    protected $authnContextClassRef;

    /**
     * @param int    $authnInstant
     * @param string $sessionIndex
     * @param string $authnContextClassRef
     */
    public function __construct($authnInstant, $sessionIndex, $authnContextClassRef)
    {
        $this->authnInstant = $authnInstant;
        $this->sessionIndex = $sessionIndex;
        $this->authnContextClassRef = $authnContextClassRef;
    }

    /**
     * @return int
     */
    public function getAuthnInstant()
    {
        return $this->authnInstant;
    }

    /**
     * @return string
     */
    public function getSessionIndex()
    {
        return $this->sessionIndex;
    }

    /**
     * @return string
     */
    public function getAuthnContextClassRef()
    {
        return $this->authnContextClassRef;
    }
}
