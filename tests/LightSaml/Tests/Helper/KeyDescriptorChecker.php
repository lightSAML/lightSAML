<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
