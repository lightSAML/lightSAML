<?php

namespace LightSaml\Meta\TrustOptions;

class TrustOptions
{
    /** @var bool */
    protected $signAuthnRequest = false;

    /** @var bool */
    protected $encryptAuthnRequest = false;

    /** @var bool */
    protected $signAssertions = true;

    /** @var bool */
    protected $encryptAssertions = true;

    /** @var bool */
    protected $signResponse = true;

    /**
     * @return boolean
     */
    public function getEncryptAssertions()
    {
        return $this->encryptAssertions;
    }

    /**
     * @param boolean $encryptAssertions
     *
     * @return TrustOptions
     */
    public function setEncryptAssertions($encryptAssertions)
    {
        $this->encryptAssertions = (bool) $encryptAssertions;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getEncryptAuthnRequest()
    {
        return $this->encryptAuthnRequest;
    }

    /**
     * @param boolean $encryptAuthnRequest
     *
     * @return TrustOptions
     */
    public function setEncryptAuthnRequest($encryptAuthnRequest)
    {
        $this->encryptAuthnRequest = (bool) $encryptAuthnRequest;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getSignAssertions()
    {
        return $this->signAssertions;
    }

    /**
     * @param boolean $signAssertions
     *
     * @return TrustOptions
     */
    public function setSignAssertions($signAssertions)
    {
        $this->signAssertions = (bool) $signAssertions;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getSignAuthnRequest()
    {
        return $this->signAuthnRequest;
    }

    /**
     * @param boolean $signAuthnRequest
     *
     * @return TrustOptions
     */
    public function setSignAuthnRequest($signAuthnRequest)
    {
        $this->signAuthnRequest = (bool) $signAuthnRequest;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getSignResponse()
    {
        return $this->signResponse;
    }

    /**
     * @param boolean $signResponse
     *
     * @return TrustOptions
     */
    public function setSignResponse($signResponse)
    {
        $this->signResponse = (bool) $signResponse;

        return $this;
    }
}
