<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\Outbound\Message\ResolveEndpointBaseAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Model\Metadata\Endpoint;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Protocol\SamlMessage;
use LightSaml\Profile\Profiles;
use LightSaml\Resolver\Endpoint\Criteria\BindingCriteria;
use LightSaml\Resolver\Endpoint\Criteria\DescriptorTypeCriteria;
use LightSaml\Resolver\Endpoint\Criteria\IndexCriteria;
use LightSaml\Resolver\Endpoint\Criteria\LocationCriteria;
use LightSaml\Resolver\Endpoint\Criteria\ServiceTypeCriteria;
use LightSaml\Resolver\Endpoint\EndpointResolverInterface;
use LightSaml\Tests\BaseTestCase;
use Psr\Log\LoggerInterface;

abstract class AbstractResolveEndpointActionTest extends BaseTestCase
{
    /** @var ResolveEndpointBaseAction|\PHPUnit\Framework\MockObject\MockObject */
    protected $action;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $logger;

    /** @var EndpointResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $endpointResolver;

    protected function setUp(): void
    {
        $this->logger = $this->getLoggerMock();
        $this->endpointResolver = $this->getMockBuilder(EndpointResolverInterface::class)->getMock();
        $this->action = $this->createAction($this->logger, $this->endpointResolver);
    }

    /**
     * @return ResolveEndpointBaseAction
     */
    abstract protected function createAction(LoggerInterface $logger, EndpointResolverInterface $endpointResolver);

    /**
     * @param bool     $shouldBeCalled
     * @param callable $callback
     */
    protected function setEndpointResolver($shouldBeCalled, $callback)
    {
        if ($shouldBeCalled) {
            $this->endpointResolver->expects($this->once())
                ->method('resolve')
                ->willReturnCallback($callback);
        } else {
            $this->endpointResolver->expects($this->never())
                ->method('resolve');
        }
    }

    /**
     * @param string           $ownRole
     * @param SamlMessage      $inboundMessage
     * @param Endpoint         $endpoint
     * @param EntityDescriptor $partyEntityDescriptor
     * @param string           $profileId
     *
     * @return \LightSaml\Context\Profile\ProfileContext
     */
    protected function createContext(
        $ownRole = ProfileContext::ROLE_IDP,
        SamlMessage $inboundMessage = null,
        Endpoint $endpoint = null,
        EntityDescriptor $partyEntityDescriptor = null,
        $profileId = Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST
    ) {
        $context = $this->getProfileContext($profileId, $ownRole);

        if ($endpoint) {
            $context->getEndpointContext()->setEndpoint($endpoint);
        }

        if (null == $partyEntityDescriptor) {
            $partyEntityDescriptor = EntityDescriptor::load(__DIR__.'/../../../../../../../resources/sample/EntityDescriptor/idp2-ed-formatted.xml');
        }
        $context->getPartyEntityContext()->setEntityDescriptor($partyEntityDescriptor);

        if ($inboundMessage) {
            $context->getInboundContext()->setMessage($inboundMessage);
        }

        return $context;
    }

    protected function criteriaSetShouldHaveBindingCriteria(CriteriaSet $criteriaSet, array $bindings)
    {
        if (empty($bindings)) {
            $this->assertFalse($criteriaSet->has(BindingCriteria::class));
        } else {
            $this->assertTrue($criteriaSet->has(BindingCriteria::class));
            /** @var BindingCriteria $criteria */
            $criteria = $criteriaSet->getSingle(BindingCriteria::class);
            $this->assertEquals($bindings, $criteria->getAllBindings());
        }
    }

    /**
     * @param string $value
     */
    protected function criteriaSetShouldHaveDescriptorTypeCriteria(CriteriaSet $criteriaSet, $value)
    {
        if ($value) {
            $this->assertTrue($criteriaSet->has(DescriptorTypeCriteria::class));
            /** @var DescriptorTypeCriteria $criteria */
            $criteria = $criteriaSet->getSingle(DescriptorTypeCriteria::class);
            $this->assertEquals($value, $criteria->getDescriptorType());
        } else {
            $this->assertFalse($criteriaSet->has(DescriptorTypeCriteria::class));
        }
    }

    /**
     * @param string $value
     */
    protected function criteriaSetShouldHaveServiceTypeCriteria(CriteriaSet $criteriaSet, $value)
    {
        if ($value) {
            $this->assertTrue($criteriaSet->has(ServiceTypeCriteria::class));
            /** @var ServiceTypeCriteria $criteria */
            $criteria = $criteriaSet->getSingle(ServiceTypeCriteria::class);
            $this->assertEquals($value, $criteria->getServiceType());
        } else {
            $this->assertFalse($criteriaSet->has(ServiceTypeCriteria::class));
        }
    }

    /**
     * @param string $value
     */
    protected function criteriaSetShouldHaveIndexCriteria(CriteriaSet $criteriaSet, $value)
    {
        if ($value) {
            $this->assertTrue($criteriaSet->has(IndexCriteria::class));
            /** @var IndexCriteria $criteria */
            $criteria = $criteriaSet->getSingle(IndexCriteria::class);
            $this->assertEquals($value, $criteria->getIndex());
        } else {
            $this->assertFalse($criteriaSet->has(IndexCriteria::class));
        }
    }

    /**
     * @param string $value
     */
    protected function criteriaSetShouldHaveLocationCriteria(CriteriaSet $criteriaSet, $value)
    {
        if ($value) {
            $this->assertTrue($criteriaSet->has(LocationCriteria::class));
            /** @var LocationCriteria $criteria */
            $criteria = $criteriaSet->getSingle(LocationCriteria::class);
            $this->assertEquals($value, $criteria->getLocation());
        } else {
            $this->assertFalse($criteriaSet->has(LocationCriteria::class));
        }
    }
}
