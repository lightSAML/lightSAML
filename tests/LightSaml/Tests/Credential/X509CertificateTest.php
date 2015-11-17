<?php

namespace LightSaml\Tests\Credential;

use LightSaml\Credential\X509Certificate;

class X509CertificateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid PEM encoded certificate
     */
    public function test__error_on_invalid_load_pem_context()
    {
        $certificate = new X509Certificate();
        $certificate->loadPem('not a pem format');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage File not found '/non/existing/file/123'
     */
    public function test_error_on_invalid_load_from_file()
    {
        $certificate = new X509Certificate();
        $certificate->loadFromFile('/non/existing/file/123');
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlException
     * @expectedExceptionMessage Certificate data not set
     */
    public function test_error_when_parse_called_with_out_data_set()
    {
        $certificate = new X509Certificate();
        $certificate->parse();
    }

    public function throws_exception_when_data_not_set_provider()
    {
        return [
            ['getFingerprint'],
            ['getInfo'],
            ['getValidToTimestamp'],
            ['getValidFromTimestamp'],
            ['getIssuer'],
            ['getSubject'],
            ['getName'],
        ];
    }

    /**
     * @dataProvider throws_exception_when_data_not_set_provider
     * @expectedException \LightSaml\Error\LightSamlException
     * @expectedExceptionMessage Certificate data not set
     */
    public function test_throws_exception_when_data_not_set($method)
    {
        $certificate = new X509Certificate();
        $certificate->{$method}();
    }
}
