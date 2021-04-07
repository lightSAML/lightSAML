<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Resolver\Credential;

use LightSaml\Credential\Context\MetadataCredentialContext;
use LightSaml\Credential\CredentialInterface;
use LightSaml\Credential\Criteria\MetadataCriteria;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Model\Metadata\IdpSsoDescriptor;
use LightSaml\Model\Metadata\SpSsoDescriptor;

class MetadataFilterResolver extends AbstractQueryableResolver
{
    /**
     * @param CredentialInterface[] $arrCredentials
     *
     * @return CredentialInterface[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $arrCredentials = [])
    {
        if (false == $criteriaSet->has(MetadataCriteria::class)) {
            return $arrCredentials;
        }

        $result = [];
        foreach ($criteriaSet->get(MetadataCriteria::class) as $criteria) {
            /* @var MetadataCriteria $criteria */
            foreach ($arrCredentials as $credential) {
                /** @var MetadataCredentialContext $metadataContext */
                $metadataContext = $credential->getCredentialContext()->get(MetadataCredentialContext::class);
                if (false == $metadataContext ||
                    MetadataCriteria::TYPE_IDP == $criteria->getMetadataType() && $metadataContext->getRoleDescriptor() instanceof IdpSsoDescriptor ||
                    MetadataCriteria::TYPE_SP == $criteria->getMetadataType() && $metadataContext->getRoleDescriptor() instanceof SpSsoDescriptor
                ) {
                    $result[] = $credential;
                }
            }
        }

        return $result;
    }
}
