<?php

namespace LightSaml\Tests\Resolver\Signature;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Credential\CredentialInterface;
use LightSaml\Credential\Criteria\EntityIdCriteria;
use LightSaml\Credential\Criteria\MetadataCriteria;
use LightSaml\Credential\Criteria\UsageCriteria;
use LightSaml\Credential\Criteria\X509CredentialCriteria;
use LightSaml\Credential\UsageType;
use LightSaml\Credential\X509Certificate;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Meta\TrustOptions\TrustOptions;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Profile\Profiles;
use LightSaml\Resolver\Credential\CredentialResolverQuery;
use LightSaml\Resolver\Signature\OwnSignatureResolver;
use LightSaml\Tests\BaseTestCase;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class OwnSignatureResolverTest extends BaseTestCase
{
    public function test_constructs_with_credential_resolver()
    {
        new OwnSignatureResolver($this->getCredentialResolverMock());
        $this->assertTrue(true);
    }

    public function test_throws_context_exception_when_no_credential_resolved()
    {
        $this->expectExceptionMessage("Unable to find signing credential");
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $signatureResolver = new OwnSignatureResolver($credentialResolverMock = $this->getCredentialResolverMock());

        $context = $this->getProfileContext();
        $context->getOwnEntityContext()->setEntityDescriptor($ownEntityDescriptor = new EntityDescriptor($ownEntityId = 'http://own.id'));

        $credentialResolverMock->method('query')->willReturn($query = new CredentialResolverQuery($credentialResolverMock));
        $credentialResolverMock->method('resolve')->willReturn([]);

        $signatureResolver->getSignature($context);
    }

    public function test_returns_signature_writer_with_first_resolved_credential()
    {
        $signatureResolver = new OwnSignatureResolver($credentialResolverMock = $this->getCredentialResolverMock());

        $context = $this->getProfileContext();
        $context->getOwnEntityContext()->setEntityDescriptor($ownEntityDescriptor = new EntityDescriptor($ownEntityId = 'http://own.id'));
        $context->getPartyEntityContext()->setTrustOptions(new TrustOptions());

        $credentialResolverMock->method('query')->willReturn($query = new CredentialResolverQuery($credentialResolverMock));
        $credentialResolverMock->method('resolve')->willReturn([
            $credential1 = $this->getX509CredentialMock(),
            $credential2 = $this->getX509CredentialMock(),
        ]);

        $credential1->expects($this->once())
            ->method('getCertificate')
            ->willReturn($certificate = new X509Certificate());
        $credential1->expects($this->once())
            ->method('getPrivateKey')
            ->willReturn($privateKey = new XMLSecurityKey(XMLSecurityKey::AES128_CBC));

        $signatureWriter = $signatureResolver->getSignature($context);

        $this->assertSame($certificate, $signatureWriter->getCertificate());
        $this->assertSame($privateKey, $signatureWriter->getXmlSecurityKey());
    }

    public function _provider()
    {
        return [
            [ProfileContext::ROLE_IDP, MetadataCriteria::TYPE_IDP],
            [ProfileContext::ROLE_SP, MetadataCriteria::TYPE_SP],
        ];
    }

    /**
     * @dataProvider _provider
     */
    public function test_credential_criterias($profileRole, $expectedMetadataType)
    {
        $signatureResolver = new OwnSignatureResolver($credentialResolverMock = $this->getCredentialResolverMock());

        $context = $this->getProfileContext(Profiles::METADATA, $profileRole);
        $context->getOwnEntityContext()->setEntityDescriptor($ownEntityDescriptor = new EntityDescriptor($ownEntityId = 'http://own.id'));
        $context->getPartyEntityContext()->setTrustOptions(new TrustOptions());

        $credentialResolverMock->method('query')->willReturn($query = new CredentialResolverQuery($credentialResolverMock));
        $credentialResolverMock->method('resolve')
            ->willReturnCallback(function (CriteriaSet $criteriaSet) use ($ownEntityId, $expectedMetadataType) {
                $this->assertCriteria($criteriaSet, EntityIdCriteria::class, 'getEntityId', $ownEntityId);
                $this->assertCriteria($criteriaSet, UsageCriteria::class, 'getUsage', UsageType::SIGNING);
                $this->assertCriteria($criteriaSet, X509CredentialCriteria::class, null, null);
                $this->assertCriteria($criteriaSet, MetadataCriteria::class, 'getMetadataType', $expectedMetadataType);

                return [$this->getX509CredentialMock()];
            });

        $signatureResolver->getSignature($context);
    }

    public function test_throws_logic_exception_when_returned_value_if_not_credential()
    {
        $this->expectExceptionMessage("Expected X509CredentialInterface but got");
        $this->expectException(\LogicException::class);
        $signatureResolver = new OwnSignatureResolver($credentialResolverMock = $this->getCredentialResolverMock());

        $context = $this->getProfileContext();
        $context->getOwnEntityContext()->setEntityDescriptor($ownEntityDescriptor = new EntityDescriptor($ownEntityId = 'http://own.id'));

        $credentialResolverMock->method('query')->willReturn($query = new CredentialResolverQuery($credentialResolverMock));
        $credentialResolverMock->method('resolve')->willReturn([$this->getMockBuilder(CredentialInterface::class)->getMock()]);

        $signatureResolver->getSignature($context);
    }
}
