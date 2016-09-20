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

class SsoState implements \Serializable
{
    /** @var string */
    protected $localSessionId;

    /** @var array */
    protected $options = [];

    /** @var SsoSessionState[] */
    protected $ssoSessions = array();

    /**
     * @return string
     */
    public function getLocalSessionId()
    {
        return $this->localSessionId;
    }

    /**
     * @param string $localSessionId
     *
     * @return SsoSessionState
     */
    public function setLocalSessionId($localSessionId)
    {
        $this->localSessionId = $localSessionId;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return SsoState
     */
    public function addOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return SsoState
     */
    public function removeOption($name)
    {
        unset($this->options[$name]);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasOption($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * @return SsoSessionState[]
     */
    public function getSsoSessions()
    {
        return $this->ssoSessions;
    }

    /**
     * @param SsoSessionState[] $ssoSessions
     *
     * @return SsoState
     */
    public function setSsoSessions(array $ssoSessions)
    {
        $this->ssoSessions = array();
        foreach ($ssoSessions as $ssoSession) {
            $this->addSsoSession($ssoSession);
        }

        return $this;
    }

    /**
     * @param SsoSessionState $ssoSessionState
     *
     * @return SsoState
     */
    public function addSsoSession(SsoSessionState $ssoSessionState)
    {
        $this->ssoSessions[] = $ssoSessionState;

        return $this;
    }

    /**
     * @param $idpEntityId
     * @param $spEntityId
     * @param $nameId
     * @param $nameIdFormat
     * @param $sessionIndex
     *
     * @return SsoSessionState[]
     */
    public function filter($idpEntityId, $spEntityId, $nameId, $nameIdFormat, $sessionIndex)
    {
        $result = array();

        foreach ($this->ssoSessions as $ssoSession) {
            if ((!$idpEntityId || $ssoSession->getIdpEntityId() === $idpEntityId) &&
                (!$spEntityId || $ssoSession->getSpEntityId() === $spEntityId) &&
                (!$nameId || $ssoSession->getNameId() === $nameId) &&
                (!$nameIdFormat || $ssoSession->getNameIdFormat() === $nameIdFormat) &&
                (!$sessionIndex || $ssoSession->getSessionIndex() === $sessionIndex)
            ) {
                $result[] = $ssoSession;
            }
        }

        return $result;
    }

    /**
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(array(
            $this->localSessionId,
            $this->ssoSessions,
            $this->options,
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
            $this->localSessionId,
            $this->ssoSessions,
            $this->options) = $data;
    }
}
