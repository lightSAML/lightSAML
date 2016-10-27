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
    public function __construct($authnInstant = 0, $sessionIndex = null, $authnContextClassRef = null)
    {
        $this->authnInstant = $authnInstant;
        $this->sessionIndex = $sessionIndex;
        $this->authnContextClassRef = $authnContextClassRef;
    }

    /**
     * @param int $authnInstant
     *
     * @return FixedSessionInfoProvider
     */
    public function setAuthnInstant($authnInstant)
    {
        $this->authnInstant = intval($authnInstant);

        return $this;
    }

    /**
     * @param string $sessionIndex
     *
     * @return FixedSessionInfoProvider
     */
    public function setSessionIndex($sessionIndex)
    {
        $this->sessionIndex = $sessionIndex;

        return $this;
    }

    /**
     * @param string $authnContextClassRef
     *
     * @return FixedSessionInfoProvider
     */
    public function setAuthnContextClassRef($authnContextClassRef)
    {
        $this->authnContextClassRef = $authnContextClassRef;

        return $this;
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
