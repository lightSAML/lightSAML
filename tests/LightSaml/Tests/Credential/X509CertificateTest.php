<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Credential;

use LightSaml\Credential\X509Certificate;
use LightSaml\Tests\BaseTestCase;

class X509CertificateTest extends BaseTestCase
{
    public function test__error_on_invalid_load_pem_context()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid PEM encoded certificate');

        $certificate = new X509Certificate();
        $certificate->loadPem('not a pem format');
    }

    public function test_error_on_invalid_load_from_file()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File not found \'/non/existing/file/123\'');

        $certificate = new X509Certificate();
        $certificate->loadFromFile('/non/existing/file/123');
    }

    public function test_error_when_parse_called_with_out_data_set()
    {
        $this->expectException(\LightSaml\Error\LightSamlException::class);
        $this->expectExceptionMessage('Certificate data not set');

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
     */
    public function test_throws_exception_when_data_not_set($method)
    {
        $this->expectException(\LightSaml\Error\LightSamlException::class);
        $this->expectExceptionMessage('Certificate data not set');

        $certificate = new X509Certificate();
        $certificate->{$method}();
    }
}
