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

use LightSaml\Error\LightSamlSecurityException;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class KeyHelper
{
    /**
     * @param string $key        Key content or key filename
     * @param string $passphrase Passphrase for the private key
     * @param bool   $isFile     true if $key is a filename of the key
     * @param string $type
     *
     * @return XMLSecurityKey
     */
    public static function createPrivateKey($key, $passphrase, $isFile = false, $type = XMLSecurityKey::RSA_SHA1)
    {
        $result = new XMLSecurityKey($type, array('type' => 'private'));
        $result->passphrase = $passphrase;
        $result->loadKey($key, $isFile, false);

        return $result;
    }

    /**
     * @param X509Certificate $certificate
     *
     * @return XMLSecurityKey
     */
    public static function createPublicKey(X509Certificate $certificate)
    {
        if (null == $certificate->getSignatureAlgorithm()) {
            throw new LightSamlSecurityException('Unrecognized certificate signature algorithm');
        }
        $key = new XMLSecurityKey($certificate->getSignatureAlgorithm(), array('type' => 'public'));
        $key->loadKey($certificate->toPem(), false, true);

        return $key;
    }

    /**
     * @param XMLSecurityKey $key
     * @param string         $algorithm
     *
     * @throws \LightSaml\Error\LightSamlSecurityException
     * @throws \InvalidArgumentException
     *
     * @return XMLSecurityKey
     */
    public static function castKey(XMLSecurityKey $key, $algorithm)
    {
        if (false == is_string($algorithm)) {
            throw new \InvalidArgumentException('Algorithm must be string');
        }

        // do nothing if algorithm is already the type of the key
        if ($key->type === $algorithm) {
            return $key;
        }

        $keyInfo = openssl_pkey_get_details($key->key);
        if ($keyInfo === false) {
            throw new LightSamlSecurityException('Unable to get key details from XMLSecurityKey.');
        }
        if (false == isset($keyInfo['key'])) {
            throw new LightSamlSecurityException('Missing key in public key details.');
        }

        $newKey = new XMLSecurityKey($algorithm, array('type' => 'public'));
        $newKey->loadKey($keyInfo['key']);

        return $newKey;
    }
}
