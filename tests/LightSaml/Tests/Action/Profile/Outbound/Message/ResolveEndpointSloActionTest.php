<?php

namespace LightSaml\Tests\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\Outbound\Message\ResolveEndpointBaseAction;
use LightSaml\Action\Profile\Outbound\Message\ResolveEndpointSloAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\IdpSsoDescriptor;
use LightSaml\Model\Metadata\SingleLogoutService;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Resolver\Endpoint\EndpointResolverInterface;
use LightSaml\State\Sso\SsoSessionState;
use Psr\Log\LoggerInterface;

class ResolveEndpointSloActionTest extends AbstractResolveEndpointActionTest
{
    /**
     * @param LoggerInterface           $logger
     * @param EndpointResolverInterface $endpointResolver
     *
     * @return ResolveEndpointBaseAction
     */
    protected function createAction(LoggerInterface $logger, EndpointResolverInterface $endpointResolver)
    {
        return new ResolveEndpointSloAction($logger, $endpointResolver);
    }

    public function test_adds_service_type_slo()
    {
        $message = new AuthnRequest();

        $context = $this->createContext(ProfileContext::ROLE_IDP, $message);
        $context->getOwnEntityContext()->setEntityDescriptor(new EntityDescriptor($ownEntityId = 'http://own.id'));
        $context->getLogoutContext()->setSsoSessionState((new SsoSessionState())->setIdpEntityId($ownEntityId));

        $this->setEndpointResolver(true, function (CriteriaSet $criteriaSet) {
            $this->criteriaSetShouldHaveServiceTypeCriteria($criteriaSet, SingleLogoutService::class);

            return [$this->getEndpointReferenceMock($endpoint = new SingleLogoutService())];
        });

        $this->action->execute($context);
    }

    public function test_adds_sp_sso_descriptor_type_when_sso_idp_entity_is_own_id()
    {
        $message = new AuthnRequest();

        $context = $this->createContext(ProfileContext::ROLE_IDP, $message);
        $context->getOwnEntityContext()->setEntityDescriptor(new EntityDescriptor($ownEntityId = 'http://own.id'));
        $context->getLogoutContext()->setSsoSessionState((new SsoSessionState())->setIdpEntityId($ownEntityId));

        $this->setEndpointResolver(true, function (CriteriaSet $criteriaSet) {
            $this->criteriaSetShouldHaveDescriptorTypeCriteria($criteriaSet, SpSsoDescriptor::class);

            return [$this->getEndpointReferenceMock($endpoint = new SingleLogoutService())];
        });

        $this->action->execute($context);
    }

    public function test_adds_idp_sso_descriptor_type_when_sso_sp_entity_is_own_id()
    {
        $message = new AuthnRequest();

        $context = $this->createContext(ProfileContext::ROLE_IDP, $message);
        $context->getOwnEntityContext()->setEntityDescriptor(new EntityDescriptor($ownEntityId = 'http://own.id'));
        $context->getLogoutContext()->setSsoSessionState((new SsoSessionState())->setSpEntityId($ownEntityId));

        $this->setEndpointResolver(true, function (CriteriaSet $criteriaSet) {
            $this->criteriaSetShouldHaveDescriptorTypeCriteria($criteriaSet, IdpSsoDescriptor::class);

            return [$this->getEndpointReferenceMock($endpoint = new SingleLogoutService())];
        });

        $this->action->execute($context);
    }

    public function test_throws_context_exception_own_entity_id_does_not_match_sso_idp_nor_sp()
    {
        $this->expectExceptionMessage("Unable to resolve logout target descriptor type");
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $message = new AuthnRequest();

        $context = $this->createContext(ProfileContext::ROLE_IDP, $message);
        $context->getOwnEntityContext()->setEntityDescriptor(new EntityDescriptor($ownEntityId = 'http://own.id'));
        $context->getLogoutContext()->setSsoSessionState((new SsoSessionState())->setSpEntityId('http://other.com'));

        $this->setEndpointResolver(false, null);

        $this->action->execute($context);
    }
}
