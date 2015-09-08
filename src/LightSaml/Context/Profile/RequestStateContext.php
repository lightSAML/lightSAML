<?php

namespace LightSaml\Context\Profile;

use LightSaml\State\Request\RequestState;

class RequestStateContext extends AbstractProfileContext
{
    /** @var  RequestState */
    protected $requestState;

    /**
     * @return RequestState
     */
    public function getRequestState()
    {
        return $this->requestState;
    }

    /**
     * @param RequestState $requestState
     *
     * @return RequestStateContext
     */
    public function setRequestState($requestState)
    {
        $this->requestState = $requestState;

        return $this;
    }
}
