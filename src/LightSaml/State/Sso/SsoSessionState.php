<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\State\Sso;

use LightSaml\Error\LightSamlException;
use LightSaml\Meta\ParameterBag;

class SsoSessionState implements \Serializable
{
    /** @var string */
    protected $idpEntityId;

    /** @var string */
    protected $spEntityId;

    /** @var string */
    protected $nameId;

    /** @var string */
    protected $nameIdFormat;

    /** @var string */
    protected $sessionIndex;

    /** @var \DateTime */
    protected $sessionInstant;

    /** @var \DateTime */
    protected $firstAuthOn;

    /** @var \DateTime */
    protected $lastAuthOn;

    /** @var ParameterBag */
    protected $parameters;

    public function __construct()
    {
        $this->parameters = new ParameterBag();
    }

    /**
     * @return string
     */
    public function getIdpEntityId()
    {
        return $this->idpEntityId;
    }

    /**
     * @param string $idpEntityId
     *
     * @return SsoSessionState
     */
    public function setIdpEntityId($idpEntityId)
    {
        $this->idpEntityId = $idpEntityId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSpEntityId()
    {
        return $this->spEntityId;
    }

    /**
     * @param string $spEntityId
     *
     * @return SsoSessionState
     */
    public function setSpEntityId($spEntityId)
    {
        $this->spEntityId = $spEntityId;

        return $this;
    }

    /**
     * @return string
     */
    public function getNameId()
    {
        return $this->nameId;
    }

    /**
     * @param string $nameId
     *
     * @return SsoSessionState
     */
    public function setNameId($nameId)
    {
        $this->nameId = $nameId;

        return $this;
    }

    /**
     * @return string
     */
    public function getNameIdFormat()
    {
        return $this->nameIdFormat;
    }

    /**
     * @param string $nameIdFormat
     *
     * @return SsoSessionState
     */
    public function setNameIdFormat($nameIdFormat)
    {
        $this->nameIdFormat = $nameIdFormat;

        return $this;
    }

    /**
     * @return string
     */
    public function getSessionIndex()
    {
        return $this->sessionIndex;
    }

    /**
     * @param string $sessionIndex
     *
     * @return SsoSessionState
     */
    public function setSessionIndex($sessionIndex)
    {
        $this->sessionIndex = $sessionIndex;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFirstAuthOn()
    {
        return $this->firstAuthOn;
    }

    /**
     * @param \DateTime $firstAuthOn
     *
     * @return SsoSessionState
     */
    public function setFirstAuthOn($firstAuthOn)
    {
        $this->firstAuthOn = $firstAuthOn;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastAuthOn()
    {
        return $this->lastAuthOn;
    }

    /**
     * @param \DateTime $lastAuthOn
     *
     * @return SsoSessionState
     */
    public function setLastAuthOn($lastAuthOn)
    {
        $this->lastAuthOn = $lastAuthOn;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSessionInstant()
    {
        return $this->sessionInstant;
    }

    /**
     * @param \DateTime $sessionInstant
     *
     * @return SsoSessionState
     */
    public function setSessionInstant($sessionInstant)
    {
        $this->sessionInstant = $sessionInstant;

        return $this;
    }

    /**
     * @return ParameterBag
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @deprecated Since 1.2, will be removed in 2.0. Use getParameters() instead
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->parameters->all();
    }

    /**
     * @deprecated Since 1.2, will be removed in 2.0. Use getParameters() instead
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return SsoSessionState
     */
    public function addOption($name, $value)
    {
        $this->parameters->set($name, $value);

        return $this;
    }

    /**
     * @deprecated Since 1.2, will be removed in 2.0. Use getParameters() instead
     *
     * @param string $name
     *
     * @return SsoSessionState
     */
    public function removeOption($name)
    {
        $this->parameters->remove($name);

        return $this;
    }

    /**
     * @deprecated Since 1.2, will be removed in 2.0. Use getParameters() instead
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasOption($name)
    {
        return $this->parameters->has($name);
    }

    /**
     * @param string $partyId
     *
     * @return string Other party id
     *
     * @throws \LightSaml\Error\LightSamlException If $partyId does not match sp or idp entity id
     */
    public function getOtherPartyId($partyId)
    {
        if ($partyId == $this->idpEntityId) {
            return $this->spEntityId;
        } elseif ($partyId == $this->spEntityId) {
            return $this->idpEntityId;
        }

        throw new LightSamlException(sprintf(
            'Party "%s" is not included in sso session between "%s" and "%s"',
            $partyId,
            $this->idpEntityId,
            $this->spEntityId
        ));
    }
    /**
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(array(
            $this->idpEntityId,
            $this->spEntityId,
            $this->nameId,
            $this->nameIdFormat,
            $this->sessionIndex,
            $this->sessionInstant,
            $this->firstAuthOn,
            $this->lastAuthOn,
            [],
            $this->parameters,
        ));
    }

    /**
     * @param string $serialized
     *
     * @return void
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 5, null));

        list(
            $this->idpEntityId,
            $this->spEntityId,
            $this->nameId,
            $this->nameIdFormat,
            $this->sessionIndex,
            $this->sessionInstant,
            $this->firstAuthOn,
            $this->lastAuthOn,
            $options,
            $this->parameters
        ) = $data;

        // if deserialized from old format, set old options to new parameters
        if ($options && $this->parameters->count() == 0) {
            $this->parameters->replace($options);
        }
    }
}
