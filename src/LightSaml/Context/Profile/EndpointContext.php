<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Context\Profile;

use LightSaml\Model\Metadata\Endpoint;

class EndpointContext extends AbstractProfileContext
{
    /** @var Endpoint */
    private $endpoint;

    /**
     * @return Endpoint|null
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param Endpoint $endpoint
     *
     * @return EndpointContext
     */
    public function setEndpoint(Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }
}
