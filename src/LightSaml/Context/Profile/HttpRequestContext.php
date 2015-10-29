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

use Symfony\Component\HttpFoundation\Request;

class HttpRequestContext extends AbstractProfileContext
{
    /** @var Request */
    private $request;

    /**
     * @return Request|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     *
     * @return HttpRequestContext
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }
}
