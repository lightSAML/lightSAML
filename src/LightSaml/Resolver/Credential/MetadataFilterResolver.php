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

use LightSaml\Model\Metadata\IdpSsoDescriptor;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Credential\Context\MetadataCredentialContext;
use LightSaml\Credential\CredentialInterface;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Credential\Criteria\MetadataCriteria;

class MetadataFilterResolver extends AbstractQueryableResolver
{
    /**
     * @param CriteriaSet           $criteriaSet
     * @param CredentialInterface[] $arrCredentials
     *
     * @return CredentialInterface[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $arrCredentials = array())
    {
        if (false == $criteriaSet->has('LightSaml\Credential\Criteria\MetadataCriteria')) {
            return $arrCredentials;
        }

        $result = array();
        foreach ($criteriaSet->get('LightSaml\Credential\Criteria\MetadataCriteria') as $criteria) {
            /* @var MetadataCriteria $criteria */
            foreach ($arrCredentials as $credential) {
                /** @var MetadataCredentialContext $metadataContext */
                $metadataContext = $credential->getCredentialContext()->get('LightSaml\Credential\Context\MetadataCredentialContext');
                if (false == $metadataContext ||
                    $criteria->getMetadataType() == MetadataCriteria::TYPE_IDP && $metadataContext->getRoleDescriptor() instanceof IdpSsoDescriptor ||
                    $criteria->getMetadataType() == MetadataCriteria::TYPE_SP && $metadataContext->getRoleDescriptor() instanceof SpSsoDescriptor
                ) {
                    $result[] = $credential;
                }
            }
        }

        return $result;
    }
}
