<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Resolver\Signature;

use LightSaml\Context\Profile\AbstractProfileContext;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlContextException;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\Resolver\Credential\CredentialResolverInterface;
use LightSaml\SamlConstants;
use LightSaml\Credential\UsageType;
use LightSaml\Credential\X509CredentialInterface;
use LightSaml\Credential\Criteria\EntityIdCriteria;
use LightSaml\Credential\Criteria\MetadataCriteria;
use LightSaml\Credential\Criteria\UsageCriteria;
use LightSaml\Credential\Criteria\X509CredentialCriteria;

class OwnSignatureResolver implements SignatureResolverInterface
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
     * @param AbstractProfileContext $context
     *
     * @return SignatureWriter
     */
    public function getSignature(AbstractProfileContext $context)
    {
        $credential = $this->getSigningCredential($context);
        if (null == $credential) {
            throw new LightSamlContextException($context, 'Unable to find signing credential');
        }
        $trustOptions = $context->getProfileContext()->getTrustOptions();

        $signature = new SignatureWriter($credential->getCertificate(), $credential->getPrivateKey(), $trustOptions->getSignatureDigestAlgorithm());

        return $signature;
    }

    /**
     * @param AbstractProfileContext $context
     *
     * @return X509CredentialInterface|null
     */
    private function getSigningCredential(AbstractProfileContext $context)
    {
        $profileContext = $context->getProfileContext();

        $entityDescriptor = $profileContext->getOwnEntityDescriptor();

        $query = $this->credentialResolver->query();
        $query
            ->add(new EntityIdCriteria($entityDescriptor->getEntityID()))
            ->add(new UsageCriteria(UsageType::SIGNING))
            ->add(new X509CredentialCriteria())
            ->addIf(ProfileContext::ROLE_IDP === $profileContext->getOwnRole(), function () {
                return new MetadataCriteria(MetadataCriteria::TYPE_IDP, SamlConstants::VERSION_20);
            })
            ->addIf(ProfileContext::ROLE_SP === $profileContext->getOwnRole(), function () {
                return new MetadataCriteria(MetadataCriteria::TYPE_SP, SamlConstants::VERSION_20);
            })
        ;
        $query->resolve();

        $result = $query->firstCredential();
        if ($result && false === $result instanceof X509CredentialInterface) {
            throw new \LogicException(sprintf('Expected X509CredentialInterface but got %s', get_class($result)));
        }

        return $result;
    }
}
