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

use Symfony\Component\HttpFoundation\Response;

class HttpResponseContext extends AbstractProfileContext
{
    /** @var Response */
    private $response;

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     *
     * @return HttpResponseContext
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }
}
