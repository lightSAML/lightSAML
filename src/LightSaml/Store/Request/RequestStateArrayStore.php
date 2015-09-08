<?php

namespace LightSaml\Store\Request;

class RequestStateArrayStore extends AbstractRequestStateArrayStore
{
    private $arrayStore = array();

    /**
     * @return array
     */
    protected function getArray()
    {
        return $this->arrayStore;
    }

    /**
     * @param array $arr
     *
     * @return AbstractRequestStateArrayStore
     */
    protected function setArray(array $arr)
    {
        $this->arrayStore = $arr;
    }
}
