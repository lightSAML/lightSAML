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
