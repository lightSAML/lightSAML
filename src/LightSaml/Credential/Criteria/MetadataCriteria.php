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

use LightSaml\SamlConstants;

class MetadataCriteria implements TrustCriteriaInterface
{
    const TYPE_IDP = 'idp';
    const TYPE_SP = 'sp';

    /** @var string */
    protected $metadataType;

    /** @var string */
    protected $protocol;

    /**
     * @param string $metadataType
     * @param string $protocol
     */
    public function __construct($metadataType, $protocol = SamlConstants::PROTOCOL_SAML2)
    {
        $this->metadataType = $metadataType;
        $this->protocol = $protocol;
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return string
     */
    public function getMetadataType()
    {
        return $this->metadataType;
    }
}
