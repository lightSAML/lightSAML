<?php

namespace LightSaml\Tests\Resolver\Endpoint;

use LightSaml\Criteria\CriteriaSet;
use LightSaml\Model\Metadata\EndpointReference;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\SingleSignOnService;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Resolver\Endpoint\BindingEndpointResolver;
use LightSaml\Resolver\Endpoint\Criteria\BindingCriteria;
use LightSaml\SamlConstants;

class BindingEndpointResolverTest extends \PHPUnit_Framework_TestCase
{
    public function test__does_not_modify_when_criteria_not_present()
    {
        $candidates = [
            $firstEndpoint = $this->getMock(EndpointReference::class, [], [], '', false),
            $secondEndpoint = $this->getMock(EndpointReference::class, [], [], '', false),
        ];

        $resolver = new BindingEndpointResolver();

        $result = $resolver->resolve(new CriteriaSet([]), $candidates);

        $this->assertCount(2, $result);
        $this->assertSame($firstEndpoint, $result[0]);
        $this->assertSame($secondEndpoint, $result[1]);
    }

    public function test__filters_by_given_bindings()
    {
        $criteriaSet = new CriteriaSet([new BindingCriteria([
            SamlConstants::BINDING_SAML2_HTTP_POST,
            SamlConstants::BINDING_SAML2_HTTP_REDIRECT
        ])]);

        $candidates = [
            $firstEndpoint = new EndpointReference(
                new EntityDescriptor(),
                new SpSsoDescriptor(),
                (new SingleSignOnService())->setBinding(SamlConstants::BINDING_SAML2_SOAP)
            ),
            $secondEndpoint = new EndpointReference(
                new EntityDescriptor(),
                new SpSsoDescriptor(),
                (new SingleSignOnService())->setBinding(SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
            ),
            $thirdEndpoint = new EndpointReference(
                new EntityDescriptor(),
                new SpSsoDescriptor(),
                (new SingleSignOnService())->setBinding(SamlConstants::BINDING_SAML2_HTTP_POST)
            ),
            $fourthEndpoint = new EndpointReference(
                new EntityDescriptor(),
                new SpSsoDescriptor(),
                (new SingleSignOnService())->setBinding(SamlConstants::BINDING_SAML2_HTTP_ARTIFACT)
            ),
        ];

        $resolver = new BindingEndpointResolver();

        $result = $resolver->resolve($criteriaSet, $candidates);

        $this->assertCount(2, $result);
        $this->assertSame($thirdEndpoint, $result[0]);
        $this->assertSame($secondEndpoint, $result[1]);
    }
}
