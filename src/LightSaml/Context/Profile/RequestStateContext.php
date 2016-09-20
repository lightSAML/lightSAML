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

use LightSaml\State\Request\RequestState;

class RequestStateContext extends AbstractProfileContext
{
    /** @var RequestState */
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
