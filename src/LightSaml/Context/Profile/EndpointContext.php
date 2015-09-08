<?php

namespace LightSaml\Context\Profile;

use LightSaml\Model\Metadata\Endpoint;

class EndpointContext extends AbstractProfileContext
{
    /** @var  Endpoint */
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
