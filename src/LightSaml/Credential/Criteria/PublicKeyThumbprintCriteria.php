<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Credential\Criteria;

class PublicKeyThumbprintCriteria implements TrustCriteriaInterface
{
    /** @var string */
    private $thumbprint;

    /**
     * @param string $thumbprint
     */
    public function __construct($thumbprint)
    {
        $this->thumbprint = $thumbprint;
    }

    /**
     * @return string
     */
    public function getThumbprint()
    {
        return $this->thumbprint;
    }
}
