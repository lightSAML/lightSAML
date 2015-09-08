<?php

namespace LightSaml\Model\Security;

use LightSaml\Error\LightSamlException;

class X509Certificate
{
    /** @var string */
    protected $data;

    /**
     * @var null|array
     */
    protected $info;

    /**
     * @param string $data
     *
     * @return X509Certificate
     */
    public function setData($data)
    {
        $this->data = preg_replace('/\s+/', '', $data);
        $this->parse();

        return $this;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     *
     * @return X509Certificate
     *
     * @throws \InvalidArgumentException
     */
    public function loadPem($data)
    {
        $pattern = '/^-----BEGIN CERTIFICATE-----([^-]*)^-----END CERTIFICATE-----/m';
        if (false == preg_match($pattern, $data, $matches)) {
            throw new \InvalidArgumentException('Invalid PEM encoded certificate');
        }
        $this->data = preg_replace('/\s+/', '', $matches[1]);
        $this->parse();

        return $this;
    }

    /**
     * @param string $filename
     *
     * @return X509Certificate
     *
     * @throws \InvalidArgumentException
     */
    public function loadFromFile($filename)
    {
        if (!is_file($filename)) {
            throw new \InvalidArgumentException(sprintf("File not found '%s'", $filename));
        }
        $content = file_get_contents($filename);
        $this->loadPem($content);

        return $this;
    }

    /**
     * @return string
     */
    public function toPem()
    {
        $result = "-----BEGIN CERTIFICATE-----\n".chunk_split($this->getData(), 64, "\n")."-----END CERTIFICATE-----\n";

        return $result;
    }

    public function parse()
    {
        if (false == $this->data) {
            throw new LightSamlException('Certificate data not set');
        }

        $this->info = openssl_x509_parse($this->toPem());
    }

    /**
     * @return string
     *
     * @throws \LightSaml\Error\LightSamlException
     */
    public function getName()
    {
        if (false == $this->info) {
            throw new LightSamlException('Certificate data not set');
        }

        return $this->info['name'];
    }

    /**
     * @return string
     *
     * @throws \LightSaml\Error\LightSamlException
     */
    public function getSubject()
    {
        if (false == $this->info) {
            throw new LightSamlException('Certificate data not set');
        }

        return $this->info['subject'];
    }

    /**
     * @return array
     * @throws \LightSaml\Error\LightSamlException
     */
    public function getIssuer()
    {
        if (false == $this->info) {
            throw new LightSamlException('Certificate data not set');
        }

        return $this->info['issuer'];
    }

    /**
     * @return int
     * @throws \LightSaml\Error\LightSamlException
     */
    public function getValidFromTimestamp()
    {
        if (false == $this->info) {
            throw new LightSamlException('Certificate data not set');
        }

        return $this->info['validFrom_time_t'];
    }

    /**
     * @return int
     *
     * @throws \LightSaml\Error\LightSamlException
     */
    public function getValidToTimestamp()
    {
        if (false == $this->info) {
            throw new LightSamlException('Certificate data not set');
        }

        return $this->info['validTo_time_t'];
    }

    /**
     * @return array
     *
     * @throws \LightSaml\Error\LightSamlException
     */
    public function getInfo()
    {
        if (false == $this->info) {
            throw new LightSamlException('Certificate data not set');
        }

        return $this->info;
    }

    /**
     * @throws \LightSaml\Error\LightSamlException
     * @return string
     */
    public function getFingerprint()
    {
        if (false == $this->data) {
            throw new LightSamlException('Certificate data not set');
        }

        return \XMLSecurityKey::getRawThumbprint($this->toPem());
    }
}
