<?php

namespace LightSaml\Tests\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\Outbound\Message\ResolveEndpointBaseAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Model\Metadata\SingleSignOnService;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\Response;
use LightSaml\Resolver\Endpoint\EndpointResolverInterface;
use LightSaml\SamlConstants;
use Psr\Log\LoggerInterface;

class ResolveEndpointBaseActionTest extends AbstractResolveEndpointActionTest
{
    public function test_does_nothing_if_endpoint_already_set()
    {
        $context = $this->getProfileContext();
        $context->getEndpointContext()->setEndpoint($endpoint = new SingleSignOnService());
        $endpoint->setLocation('http://location.com');
        $endpoint->setBinding(SamlConstants::BINDING_SAML2_HTTP_POST);

        $this->logger->expects($this->once())
            ->method('debug')
            ->with(
                'Endpoint already set with location "http://location.com" and binding "urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"',
                $this->isType('array')
            )
        ;

        $this->setEndpointResolver(false, null);

        $this->action->execute($context);
    }

    public function test_should_resolve_endpoint_and_set_to_context()
    {
        $message = new Response();
        $context = $this->createContext(ProfileContext::ROLE_IDP, $message);

        $endpoint = null;
        $this->setEndpointResolver(true, function (CriteriaSet $criteriaSet, array $endpointCandidates) use (&$endpoint) {
            $this->criteriaSetShouldHaveBindingCriteria(
                $criteriaSet,
                [SamlConstants::BINDING_SAML2_HTTP_POST, SamlConstants::BINDING_SAML2_HTTP_REDIRECT]
            );
            $this->criteriaSetShouldHaveDescriptorTypeCriteria($criteriaSet, SpSsoDescriptor::class);
            $this->criteriaSetShouldHaveServiceTypeCriteria($criteriaSet, null);

            return [$this->getEndpointReferenceMock($endpoint = new SingleSignOnService())];
        });

        $this->action->execute($context);

        $this->assertSame($endpoint, $context->getEndpoint());
    }

    public function test_throws_context_exception_when_no_endpoint_resolved()
    {
        $this->expectExceptionMessage("Unable to determine endpoint for entity 'https://B1.bead.loc/adfs/services/trust'");
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $message = new Response();
        $context = $this->createContext(ProfileContext::ROLE_IDP, $message);

        $endpoint = null;
        $this->setEndpointResolver(true, function () {
            return [];
        });

        $this->action->execute($context);
    }

    public function test_adds_index_criteria_for_authn_request_with_acs_index()
    {
        $message = new AuthnRequest();
        $message->setAssertionConsumerServiceIndex($index = 2);
        $context = $this->createContext(ProfileContext::ROLE_IDP, $message);

        $this->setEndpointResolver(true, function (CriteriaSet $criteriaSet) use ($index) {
            $this->criteriaSetShouldHaveIndexCriteria($criteriaSet, $index);

            return [$this->getEndpointReferenceMock($endpoint = new SingleSignOnService())];
        });

        $this->action->execute($context);
    }

    public function test_adds_location_criteria_for_authn_request_with_acs_url()
    {
        $message = new AuthnRequest();
        $message->setAssertionConsumerServiceURL($url = 'http://domain.com/acs');
        $context = $this->createContext(ProfileContext::ROLE_IDP, $message);

        $this->setEndpointResolver(true, function (CriteriaSet $criteriaSet) use ($url) {
            $this->criteriaSetShouldHaveLocationCriteria($criteriaSet, $url);

            return [$this->getEndpointReferenceMock($endpoint = new SingleSignOnService())];
        });

        $this->action->execute($context);
    }

    /**
     * @param LoggerInterface           $logger
     * @param EndpointResolverInterface $endpointResolver
     *
     * @return ResolveEndpointBaseAction
     */
    protected function createAction(LoggerInterface $logger, EndpointResolverInterface $endpointResolver)
    {
        return $this->getMockForAbstractClass(
            ResolveEndpointBaseAction::class,
            [$logger, $endpointResolver]
        );
    }
}
