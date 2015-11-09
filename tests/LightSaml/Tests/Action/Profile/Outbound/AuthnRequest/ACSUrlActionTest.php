<?php

namespace LightSaml\Tests\Action\Profile\Outbound\AuthnRequest;

use LightSaml\Action\Profile\Outbound\AuthnRequest\ACSUrlAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Profile\Profiles;
use LightSaml\Resolver\Endpoint\Criteria\BindingCriteria;
use LightSaml\Resolver\Endpoint\Criteria\DescriptorTypeCriteria;
use LightSaml\Resolver\Endpoint\Criteria\ServiceTypeCriteria;
use LightSaml\SamlConstants;
use LightSaml\Tests\TestHelper;

class ACSUrlActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger_and_endpoint_resolver()
    {
        new ACSUrlAction(TestHelper::getLoggerMock($this), $this->getEndpointResolverMock());
    }

    public function test_finds_acs_endpoint_and_sets_outbounding_authn_request_acs_url()
    {
        $action = new ACSUrlAction(
            $loggerMock = TestHelper::getLoggerMock($this),
            $endpointResolverMock = $this->getEndpointResolverMock()
        );

        $context = new ProfileContext(Profiles::SSO_SP_SEND_AUTHN_REQUEST, ProfileContext::ROLE_SP);
        $context->getOwnEntityContext()->setEntityDescriptor($entityDescriptorMock = $this->getEntityDescriptorMock());

        $entityDescriptorMock->expects($this->once())
            ->method('getAllEndpoints')
            ->willReturn([TestHelper::getEndpointReferenceMock($this, $endpoint = new AssertionConsumerService('http://localhost/acs'))]);

        $endpointResolverMock->expects($this->once())
            ->method('resolve')
            ->with($this->isInstanceOf(CriteriaSet::class), $this->isType('array'))
            ->willReturnCallback(function (CriteriaSet $criteriaSet, array $candidates) {
                $this->assertTrue($criteriaSet->has(DescriptorTypeCriteria::class));
                $this->assertEquals(SpSsoDescriptor::class, $criteriaSet->getSingle(DescriptorTypeCriteria::class)->getDescriptorType());

                $this->assertTrue($criteriaSet->has(ServiceTypeCriteria::class));
                $this->assertEquals(AssertionConsumerService::class, $criteriaSet->getSingle(ServiceTypeCriteria::class)->getServiceType());

                $this->assertTrue($criteriaSet->has(BindingCriteria::class));
                $this->assertEquals([SamlConstants::BINDING_SAML2_HTTP_POST], $criteriaSet->getSingle(BindingCriteria::class)->getAllBindings());

                return $candidates;
            })
        ;
        $context->getOutboundContext()->setMessage($authnRequest = new AuthnRequest());

        $action->execute($context);

        $this->assertEquals($endpoint->getLocation(), $authnRequest->getAssertionConsumerServiceURL());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing ACS Service with HTTP POST binding in own SP SSO Descriptor
     */
    public function test_throws_context_exception_if_no_own_acs_service()
    {
        $action = new ACSUrlAction(
            $loggerMock = TestHelper::getLoggerMock($this),
            $endpointResolverMock = $this->getEndpointResolverMock()
        );

        $context = new ProfileContext(Profiles::SSO_SP_SEND_AUTHN_REQUEST, ProfileContext::ROLE_SP);
        $context->getOwnEntityContext()->setEntityDescriptor($entityDescriptorMock = $this->getEntityDescriptorMock());

        $entityDescriptorMock->expects($this->once())
            ->method('getAllEndpoints')
            ->willReturn([]);

        $endpointResolverMock->expects($this->once())
            ->method('resolve')
            ->willReturn([]);

        $loggerMock->expects($this->once())
            ->method('error');

        $action->execute($context);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EntityDescriptor
     */
    private function getEntityDescriptorMock()
    {
        return $this->getMock(EntityDescriptor::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Resolver\Endpoint\EndpointResolverInterface
     */
    private function getEndpointResolverMock()
    {
        return $this->getMock(\LightSaml\Resolver\Endpoint\EndpointResolverInterface::class);
    }
}
