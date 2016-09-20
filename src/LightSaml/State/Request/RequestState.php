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

class RequestState implements \Serializable
{
    /** @var string */
    protected $id;

    /** @var mixed */
    protected $nonce;

    /**
     * @param string $id
     * @param mixed  $nonce
     */
    public function __construct($id = null, $nonce = null)
    {
        $this->id = $id;
        $this->nonce = $nonce;
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
     * @param mixed $nonce
     *
     * @return RequestState
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNonce()
    {
        return $this->nonce;
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
        return serialize(array($this->id, $this->nonce));
    }

    /**
     * @param string $serialized The string representation of the object
     *
     * @return void
     */
    public function unserialize($serialized)
    {
        list($this->id, $this->nonce) = unserialize($serialized);
    }
}
