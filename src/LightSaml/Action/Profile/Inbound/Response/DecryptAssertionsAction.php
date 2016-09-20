<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Inbound\Response;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Error\LightSamlContextException;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Assertion\EncryptedAssertionReader;
use LightSaml\Resolver\Credential\CredentialResolverInterface;
use LightSaml\SamlConstants;
use LightSaml\Credential\CredentialInterface;
use LightSaml\Credential\UsageType;
use LightSaml\Credential\Criteria\EntityIdCriteria;
use LightSaml\Credential\Criteria\MetadataCriteria;
use LightSaml\Credential\Criteria\UsageCriteria;
use Psr\Log\LoggerInterface;

class DecryptAssertionsAction extends AbstractProfileAction
{
    /** @var CredentialResolverInterface */
    protected $credentialResolver;

    /**
     * @param LoggerInterface             $logger
     * @param CredentialResolverInterface $credentialResolver
     */
    public function __construct(LoggerInterface $logger, CredentialResolverInterface $credentialResolver)
    {
        parent::__construct($logger);

        $this->credentialResolver = $credentialResolver;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $response = MessageContextHelper::asResponse($context->getInboundContext());

        if (count($response->getAllEncryptedAssertions()) === 0) {
            $this->logger->debug('Response has no encrypted assertions', LogHelper::getActionContext($context, $this));

            return;
        }

        $ownEntityDescriptor = $context->getOwnEntityDescriptor();

        $query = $this->credentialResolver->query();
        $query
            ->add(new EntityIdCriteria($ownEntityDescriptor->getEntityID()))
            ->add(new MetadataCriteria(
                ProfileContext::ROLE_IDP === $context->getOwnRole()
                ? MetadataCriteria::TYPE_IDP
                : MetadataCriteria::TYPE_SP,
                SamlConstants::PROTOCOL_SAML2
            ))
            ->add(new UsageCriteria(UsageType::ENCRYPTION))
        ;
        $query->resolve();
        $privateKeys = $query->getPrivateKeys();
        if (empty($privateKeys)) {
            $message = 'No credentials resolved for assertion decryption';
            $this->logger->emergency($message, LogHelper::getActionErrorContext($context, $this));
            throw new LightSamlContextException($context, $message);
        }
        $this->logger->info('Trusted decryption candidates', LogHelper::getActionContext($context, $this, array(
            'credentials' => array_map(function (CredentialInterface $credential) {
                return sprintf(
                    "Entity: '%s'; PK X509 Thumb: '%s'",
                    $credential->getEntityId(),
                    $credential->getPublicKey() ? $credential->getPublicKey()->getX509Thumbprint() : ''
                );
            }, $privateKeys),
        )));

        foreach ($response->getAllEncryptedAssertions() as $index => $encryptedAssertion) {
            if ($encryptedAssertion instanceof EncryptedAssertionReader) {
                $name = sprintf('assertion_encrypted_%s', $index);
                /** @var DeserializationContext $deserializationContext */
                $deserializationContext = $context->getInboundContext()->getSubContext($name, DeserializationContext::class);
                $assertion = $encryptedAssertion->decryptMultiAssertion($privateKeys, $deserializationContext);
                $response->addAssertion($assertion);

                $this->logger->info(
                    'Assertion decrypted',
                    LogHelper::getActionContext($context, $this, array(
                        'assertion' => $deserializationContext->getDocument()->saveXML(),
                    ))
                );
            }
        }
    }
}
