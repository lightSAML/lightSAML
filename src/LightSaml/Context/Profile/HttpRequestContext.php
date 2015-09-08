<?php

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
