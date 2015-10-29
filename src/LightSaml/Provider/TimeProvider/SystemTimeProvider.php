<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Provider\TimeProvider;

class SystemTimeProvider implements TimeProviderInterface
{
    /**
     * @return int
     */
    public function getTimestamp()
    {
        return time();
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return new \DateTime();
    }
}
