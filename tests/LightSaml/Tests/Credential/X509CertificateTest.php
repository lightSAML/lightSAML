<?php

namespace LightSaml\Tests\Credential;

use LightSaml\Credential\X509Certificate;

class X509CertificateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid PEM encoded certificate
     */
    public function testErrorOnInvalidLoadPemContext()
    {
        $certificate = new X509Certificate();
        $certificate->loadPem('not a pem format');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage File not found '/non/existing/file/123'
     */
    public function testErrorOnInvalidLoadFromFile()
    {
        $certificate = new X509Certificate();
        $certificate->loadFromFile('/non/existing/file/123');
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlException
     * @expectedExceptionMessage Certificate data not set
     */
    public function testErrorWhenParseCalledWithOutDataSet()
    {
        $certificate = new X509Certificate();
        $certificate->parse();
    }
}
