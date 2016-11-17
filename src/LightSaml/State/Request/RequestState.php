<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\State\Request;

use LightSaml\Meta\ParameterBag;

class RequestState implements \Serializable
{
    /** @var string */
    private $id;

    /** @var ParameterBag */
    private $parameters;

    /**
     * @param string $id
     * @param mixed  $nonce
     */
    public function __construct($id = null, $nonce = null)
    {
        $this->id = $id;
        $this->parameters = new ParameterBag();
        if ($nonce) {
            $this->parameters->set('nonce', $nonce);
        }
    }

    /**
     * @param string $id
     *
     * @return RequestState
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ParameterBag
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @deprecated Since 1.2, to be removed in 2.0. Use getParameters() instead
     *
     * @param mixed $nonce
     *
     * @return RequestState
     */
    public function setNonce($nonce)
    {
        $this->parameters->set('nonce', $nonce);

        return $this;
    }

    /**
     * @deprecated Since 1.2, to be removed in 2.0. Use getParameters() instead
     *
     * @return mixed
     */
    public function getNonce()
    {
        return $this->parameters->get('nonce');
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object.
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     *
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        $nonce = $this->getNonce();

        return serialize(array($this->id, $nonce, $this->parameters->serialize()));
    }

    /**
     * @param string $serialized The string representation of the object
     *
     * @return void
     */
    public function unserialize($serialized)
    {
        $nonce = null;
        $this->parameters = new ParameterBag();
        list($this->id, $nonce, $parameters) = unserialize($serialized);
        $this->parameters->unserialize($parameters);
    }
}
