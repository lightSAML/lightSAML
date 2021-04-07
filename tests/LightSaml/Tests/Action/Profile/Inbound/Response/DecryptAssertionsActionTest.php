<?php

namespace LightSaml\Tests\Action\Profile\Inbound\Response;

use LightSaml\Action\Profile\Inbound\Response\DecryptAssertionsAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Credential\Criteria\EntityIdCriteria;
use LightSaml\Credential\Criteria\MetadataCriteria;
use LightSaml\Credential\Criteria\UsageCriteria;
use LightSaml\Credential\UsageType;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Protocol\Response;
use LightSaml\Profile\Profiles;
use LightSaml\Resolver\Credential\CredentialResolverQuery;
use LightSaml\Tests\BaseTestCase;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class DecryptAssertionsActionTest extends BaseTestCase
{
    public function test_constructs_with_logger_and_credential_resolver()
    {
        new DecryptAssertionsAction($this->getLoggerMock(), $this->getCredentialResolverMock());
        $this->assertTrue(true);
    }

    public function resolves_credentials_for_own_entity_id_party_role_and_encryption_usage_provider()
    {
        return [
            [ProfileContext::ROLE_IDP, MetadataCriteria::TYPE_IDP],
            [ProfileContext::ROLE_SP, MetadataCriteria::TYPE_SP],
        ];
    }

    /**
     * @dataProvider resolves_credentials_for_own_entity_id_party_role_and_encryption_usage_provider
     */
    public function test_resolves_credentials_and_decrypts_assertions($ownRole, $expectedMetadataCriteria)
    {
        $action = new DecryptAssertionsAction(
            $loggerMock = $this->getLoggerMock(),
            $credentialResolverMock = $this->getCredentialResolverMock()
        );

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, $ownRole);
        $context->getOwnEntityContext()->setEntityDescriptor(new EntityDescriptor($entityId = 'http://entity.id'));

        $context->getInboundContext()->setMessage($response = new Response());
        $response->addEncryptedAssertion($encryptedAssertionMock1 = $this->getEncryptedAssertionReaderMock());

        $encryptedAssertionMock1->expects($this->once())
            ->method('decryptMultiAssertion')
            ->willReturn($decryptedAssertion = new Assertion());

        $credentialResolverMock->expects($this->once())
            ->method('query')
            ->willReturn($query = new CredentialResolverQuery($credentialResolverMock));
        $credentialResolverMock->expects($this->once())
            ->method('resolve')
            ->with($query)
            ->willReturn($credentials = [
                $credentialMock1 = $this->getCredentialMock(),
            ]);

        $credentialMock1->expects($this->any())
            ->method('getPrivateKey')
            ->willReturn($privateKey = new XMLSecurityKey(XMLSecurityKey::TRIPLEDES_CBC));

        $action->execute($context);

        $this->assertTrue($query->has(EntityIdCriteria::class));
        $this->assertEquals($entityId, $query->getSingle(EntityIdCriteria::class)->getEntityId());

        $this->assertTrue($query->has(MetadataCriteria::class));
        $this->assertEquals($expectedMetadataCriteria, $query->getSingle(MetadataCriteria::class)->getMetadataType());

        $this->assertTrue($query->has(UsageCriteria::class));
        $this->assertEquals(UsageType::ENCRYPTION, $query->getSingle(UsageCriteria::class)->getUsage());

        $this->assertCount(1, $response->getAllAssertions());
        $this->assertSame($decryptedAssertion, $response->getFirstAssertion());
    }

    public function test_does_nothing_if_no_encrypted_assertions()
    {
        $action = new DecryptAssertionsAction(
            $loggerMock = $this->getLoggerMock(),
            $credentialResolverMock = $this->getCredentialResolverMock()
        );

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getOwnEntityContext()->setEntityDescriptor(new EntityDescriptor($entityId = 'http://entity.id'));

        $context->getInboundContext()->setMessage($response = new Response());

        $loggerMock->expects($this->once())
            ->method('debug')
            ->with('Response has no encrypted assertions', $this->isType('array'));

        $action->execute($context);
    }

    public function test_throws_context_exception_when_no_credentials_resolved()
    {
        $this->expectExceptionMessage("No credentials resolved for assertion decryption");
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $action = new DecryptAssertionsAction(
            $loggerMock = $this->getLoggerMock(),
            $credentialResolverMock = $this->getCredentialResolverMock()
        );

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getOwnEntityContext()->setEntityDescriptor(new EntityDescriptor($entityId = 'http://entity.id'));

        $context->getInboundContext()->setMessage($response = new Response());
        $response->addEncryptedAssertion($encryptedAssertionMock1 = $this->getEncryptedAssertionReaderMock());

        $credentialResolverMock->expects($this->once())
            ->method('query')
            ->willReturn($query = new CredentialResolverQuery($credentialResolverMock));

        $credentialResolverMock->expects($this->once())
            ->method('resolve')
            ->with($query)
            ->willReturn([]);

        $action->execute($context);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Credential\CredentialInterface
     */
    private function getCredentialMock()
    {
        return $this->getMockBuilder(\LightSaml\Credential\CredentialInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Model\Assertion\EncryptedAssertionReader
     */
    private function getEncryptedAssertionReaderMock()
    {
        return $this->getMockBuilder(\LightSaml\Model\Assertion\EncryptedAssertionReader::class)->getMock();
    }
}
