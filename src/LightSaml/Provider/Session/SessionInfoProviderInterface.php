<?php

namespace LightSaml\Provider\Session;

interface SessionInfoProviderInterface
{
    /**
     * @return int
     */
    public function getAuthnInstant();

    /**
     * @return string
     */
    public function getSessionIndex();

    /**
     * @return string
     */
    public function getAuthnContextClassRef();
}
