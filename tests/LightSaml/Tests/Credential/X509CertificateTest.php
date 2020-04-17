<?php

namespace LightSaml\Tests\Credential;

use LightSaml\Credential\X509Certificate;
use LightSaml\Tests\BaseTestCase;

class X509CertificateTest extends BaseTestCase
{
    public function test__error_on_invalid_load_pem_context()
    {
        $this->expectExceptionMessage("Invalid PEM encoded certificate");
        $this->expectException(\InvalidArgumentException::class);
        $certificate = new X509Certificate();
        $certificate->loadPem('not a pem format');
    }

    public function test_error_on_invalid_load_from_file()
    {
        $this->expectExceptionMessage("File not found '/non/existing/file/123'");
        $this->expectException(\InvalidArgumentException::class);
        $certificate = new X509Certificate();
        $certificate->loadFromFile('/non/existing/file/123');
    }

    public function test_error_when_parse_called_with_out_data_set()
    {
        $this->expectExceptionMessage("Certificate data not set");
        $this->expectException(\LightSaml\Error\LightSamlException::class);
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
     *
     *
     */
    public function test_throws_exception_when_data_not_set($method)
    {
        $this->expectExceptionMessage("Certificate data not set");
        $this->expectException(\LightSaml\Error\LightSamlException::class);
        $certificate = new X509Certificate();
        $certificate->{$method}();
    }
}
