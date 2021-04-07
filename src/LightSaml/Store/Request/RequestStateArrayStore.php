<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Store\Request;

class RequestStateArrayStore extends AbstractRequestStateArrayStore
{
    private $arrayStore = [];

    /**
     * @return array
     */
    protected function getArray()
    {
        return $this->arrayStore;
    }

    /**
     * @return AbstractRequestStateArrayStore
     */
    protected function setArray(array $arr)
    {
        $this->arrayStore = $arr;
    }
}
