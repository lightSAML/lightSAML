<?php

namespace LightSaml\Tests\Helper;

use LightSaml\Model\Metadata\KeyDescriptor;
use LightSaml\Tests\BaseTestCase;

class KeyDescriptorChecker
{
    public static function checkCertificateCN(BaseTestCase $test, $use, $cn, KeyDescriptor $kd = null)
    {
        $test->assertNotNull($kd);
        $test->assertEquals($use, $kd->getUse());
        $test->assertNotEmpty($kd->getCertificate()->getData());
        $crt = openssl_x509_parse($kd->getCertificate()->toPem());
        $test->assertEquals($cn, $crt['subject']['CN']);
    }
}
