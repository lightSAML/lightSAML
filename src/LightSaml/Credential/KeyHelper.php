<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Credential;

class KeyHelper
{
    /**
     * @param string $key        Key content or key filename
     * @param string $passphrase Passphrase for the private key
     * @param bool   $isFile     true if $key is a filename of the key
     *
     * @return \XMLSecurityKey
     */
    public static function createPrivateKey($key, $passphrase, $isFile = false, $type = \XMLSecurityKey::RSA_SHA1)
    {
        $result = new \XMLSecurityKey($type, array('type' => 'private'));
        $result->passphrase = $passphrase;
        $result->loadKey($key, $isFile, false);

        return $result;
    }

    /**
     * @param X509Certificate $certificate
     *
     * @return \XMLSecurityKey
     */
    public static function createPublicKey(X509Certificate $certificate, $type = \XMLSecurityKey::RSA_SHA1)
    {
        $key = new \XMLSecurityKey($type, array('type' => 'public'));
        $key->loadKey($certificate->toPem(), false, true);

        return $key;
    }
}
