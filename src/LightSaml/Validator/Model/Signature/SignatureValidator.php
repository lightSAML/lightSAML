<?php

namespace LightSaml\Validator\Model\Signature;

use LightSaml\Error\LightSamlSecurityException;
use LightSaml\Model\XmlDSig\AbstractSignatureReader;
use LightSaml\Resolver\Credential\CredentialResolverInterface;
use LightSaml\SamlConstants;
use LightSaml\Credential\UsageType;
use LightSaml\Credential\X509Credential;
use LightSaml\Credential\X509Certificate;
use LightSaml\Credential\Criteria\EntityIdCriteria;
use LightSaml\Credential\Criteria\MetadataCriteria;
use LightSaml\Credential\Criteria\UsageCriteria;

class SignatureValidator implements SignatureValidatorInterface
{
    /** @var  CredentialResolverInterface */
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
     * @return \XMLSecurityKey|null
     */
    public function validate(AbstractSignatureReader $signature, $issuer, $metadataType)
    {
        $query = $this->credentialResolver->query();
        $query
            ->add(new EntityIdCriteria($issuer))
            ->add(new MetadataCriteria($metadataType, SamlConstants::VERSION_20))
            ->add(new UsageCriteria(UsageType::SIGNING))
        ;
        $query->resolve();

        $publicKeys = $query->getPublicKeys();
        if (empty($publicKeys)) {
            throw new LightSamlSecurityException('No credentials resolved for signature verification');
        }
        $key = $signature->validateMulti($publicKeys);

        return $key;
    }
}
