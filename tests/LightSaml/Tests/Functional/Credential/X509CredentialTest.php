<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Functional\Credential;

use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Credential\X509Credential;
use LightSaml\Tests\BaseTestCase;

class X509CredentialTest extends BaseTestCase
{
    public function test_public_key()
    {
        $certificate = new X509Certificate();
        $certificate->loadFromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml.crt');

        $credential = new X509Credential($certificate);

        $this->assertSame($certificate, $credential->getCertificate());
        $this->assertNotNull($credential->getPublicKey());
        $this->assertEquals($certificate->toPem(), $credential->getPublicKey()->getX509Certificate());

        $this->assertNull($credential->getPrivateKey());

        $this->assertEquals(['/C=RS/ST=Serbia/O=BOS/CN=mt.evo.team'], $credential->getKeyNames());
    }

    public function test_private_key()
    {
        $certificate = new X509Certificate();
        $certificate->loadFromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml.crt');

        $privateKey = KeyHelper::createPrivateKey(__DIR__.'/../../../../../resources/sample/Certificate/saml.pem', null, true);

        $credential = new X509Credential($certificate, $privateKey);

        $this->assertSame($certificate, $credential->getCertificate());
        $this->assertNotNull($credential->getPublicKey());
        $this->assertEquals($certificate->toPem(), $credential->getPublicKey()->getX509Certificate());

        $this->assertNotNull($credential->getPrivateKey());
    }
}
