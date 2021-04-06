<?php

namespace LightSaml\Tests\Action\Profile\Inbound\Message;

use LightSaml\Action\Profile\Inbound\Message\AbstractDestinationValidatorAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\IdpSsoDescriptor;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Profile\Profiles;
use LightSaml\Resolver\Endpoint\Criteria\DescriptorTypeCriteria;
use LightSaml\Resolver\Endpoint\Criteria\LocationCriteria;
use LightSaml\Resolver\Endpoint\EndpointResolverInterface;
use LightSaml\Tests\BaseTestCase;

class AbstractDestinationValidatorActionTest extends BaseTestCase
{
    public function test_constructs_with_logger_and_endpoint_resolver()
    {
        $this->getMockForAbstractClass(
            AbstractDestinationValidatorAction::class,
            [
                $this->getLoggerMock(),
                $this->getEndpointResolverMock(),
            ]
        );
        $this->assertTrue(true);
    }

    public function test_passes_if_inbound_message_destination_is_empty()
    {
        $loggerMock = $this->getLoggerMock();
        $endpointResolverMock = $this->getEndpointResolverMock();
        /** @var AbstractDestinationValidatorAction $action */
        $action = $this->getMockForAbstractClass(AbstractDestinationValidatorAction::class, [$loggerMock, $endpointResolverMock]);

        $context = $this->buildContext(ProfileContext::ROLE_IDP, null);

        $action->execute($context);

        $this->assertTrue(true);
    }

    public function test_passes_if_message_destination_matches_to_one_of_own_locations()
    {
        $loggerMock = $this->getLoggerMock();
        $endpointResolverMock = $this->getEndpointResolverMock();
        /** @var AbstractDestinationValidatorAction $action */
        $action = $this->getMockForAbstractClass(AbstractDestinationValidatorAction::class, [$loggerMock, $endpointResolverMock]);

        $context = $this->buildContext(ProfileContext::ROLE_IDP, $expectedDestination = 'http://localhost/foo');

        $endpointResolverMock->expects($this->once())
            ->method('resolve')
            ->with(
                $this->isInstanceOf(CriteriaSet::class),
                $this->isType('array')
            )
            ->willReturn(true);

        $action->execute($context);
    }

    public function makes_descriptor_type_criteria_for_own_role_provider()
    {
        return [
           [ProfileContext::ROLE_IDP, IdpSsoDescriptor::class],
           [ProfileContext::ROLE_SP, SpSsoDescriptor::class],
        ];
    }

    /**
     * @dataProvider makes_descriptor_type_criteria_for_own_role_provider
     */
    public function test_makes_descriptor_type_criteria_for_own_role($ownRole, $descriptorType)
    {
        $loggerMock = $this->getLoggerMock();
        $endpointResolverMock = $this->getEndpointResolverMock();
        /** @var AbstractDestinationValidatorAction $action */
        $action = $this->getMockForAbstractClass(AbstractDestinationValidatorAction::class, [$loggerMock, $endpointResolverMock]);

        $context = $this->buildContext($ownRole, $expectedDestination = 'http://localhost/foo');

        $endpointResolverMock->expects($this->once())
            ->method('resolve')
            ->willReturnCallback(function (CriteriaSet $criteriaSet, array $endpoints) use ($descriptorType, $expectedDestination) {
                $this->assertTrue($criteriaSet->has(LocationCriteria::class));
                $arr = $criteriaSet->get(LocationCriteria::class);
                $this->assertCount(1, $arr);
                /** @var LocationCriteria $criteria */
                $criteria = $arr[0];
                $this->assertEquals($expectedDestination, $criteria->getLocation());

                $this->assertTrue($criteriaSet->has(DescriptorTypeCriteria::class));
                $arr = $criteriaSet->get(DescriptorTypeCriteria::class);
                $this->assertCount(1, $arr);
                /** @var DescriptorTypeCriteria $criteria */
                $criteria = $arr[0];
                $this->assertInstanceOf(DescriptorTypeCriteria::class, $criteria);
                $this->assertEquals($descriptorType, $criteria->getDescriptorType());

                return true;
            });

        $action->execute($context);
    }

    public function test_throws_exception_when_destination_does_not_match()
    {
        $this->expectExceptionMessage("Invalid inbound message destination \"http://localhost/foo\"");
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $loggerMock = $this->getLoggerMock();
        $endpointResolverMock = $this->getEndpointResolverMock();
        /** @var AbstractDestinationValidatorAction $action */
        $action = $this->getMockForAbstractClass(AbstractDestinationValidatorAction::class, [$loggerMock, $endpointResolverMock]);

        $context = $this->buildContext(ProfileContext::ROLE_IDP, $expectedDestination = 'http://localhost/foo');

        $endpointResolverMock->expects($this->once())
            ->method('resolve')
            ->willReturn(false);

        $action->execute($context);
    }

    /**
     * @param string $ownRole
     * @param string $destination
     *
     * @return ProfileContext
     */
    private function buildContext($ownRole, $destination)
    {
        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, $ownRole);
        $context->getInboundContext()->setMessage(new AuthnRequest());
        if ($destination) {
            $context->getInboundMessage()->setDestination($destination);
        }

        $context->getOwnEntityContext()->setEntityDescriptor(new EntityDescriptor());

        return $context;
    }

}
