<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Validator\Model\Signature;

use LightSaml\Credential\CredentialInterface;
use LightSaml\Credential\Criteria\PublicKeyThumbprintCriteria;
use LightSaml\Error\LightSamlSecurityException;
use LightSaml\Model\XmlDSig\AbstractSignatureReader;
use LightSaml\Resolver\Credential\CredentialResolverInterface;
use LightSaml\SamlConstants;
use LightSaml\Credential\UsageType;
use LightSaml\Credential\Criteria\EntityIdCriteria;
use LightSaml\Credential\Criteria\MetadataCriteria;
use LightSaml\Credential\Criteria\UsageCriteria;

class SignatureValidator implements SignatureValidatorInterface
{
    /** @var CredentialResolverInterface */
    protected $credentialResolver;

    /**
     * @param CredentialResolverInterface $credentialResolver
     */
    public function __construct(CredentialResolverInterface $credentialResolver)
    {
        $this->credentialResolver = $credentialResolver;
    }

    /**
     * @param AbstractSignatureReader $signature
     * @param string                  $issuer
     * @param string                  $metadataType
     *
     * @return CredentialInterface|null
     */
    public function validate(AbstractSignatureReader $signature, $issuer, $metadataType)
    {
        $query = $this->credentialResolver->query();
        $query
            ->add(new EntityIdCriteria($issuer))
            ->add(new MetadataCriteria($metadataType, SamlConstants::VERSION_20))
            ->add(new UsageCriteria(UsageType::SIGNING))
        ;
        if ($signature->getKey() && $signature->getKey()->getX509Thumbprint()) {
            $query->add(new PublicKeyThumbprintCriteria($signature->getKey()->getX509Thumbprint()));
        }
        $query->resolve();

        $credentialCandidates = $query->allCredentials();
        if (empty($credentialCandidates)) {
            throw new LightSamlSecurityException('No credentials resolved for signature verification');
        }
        $credential = $signature->validateMulti($credentialCandidates);

        return $credential;
    }
}
