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
use LightSaml\Action\Profile\Outbound\Message\ResolveEndpointIdpSsoAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Model\Metadata\SingleSignOnService;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Resolver\Endpoint\EndpointResolverInterface;
use Psr\Log\LoggerInterface;

class ResolveEndpointIdpSsoActionTest extends AbstractResolveEndpointActionTest
{
    public function test_adds_service_type_sso()
    {
        $message = new AuthnRequest();
        $context = $this->createContext(ProfileContext::ROLE_IDP, $message);

        $this->setEndpointResolver(true, function (CriteriaSet $criteriaSet) {
            $this->criteriaSetShouldHaveServiceTypeCriteria($criteriaSet, SingleSignOnService::class);

            return [$this->getEndpointReferenceMock($endpoint = new SingleSignOnService())];
        });

        $this->action->execute($context);
    }

    /**
     * @return ResolveEndpointBaseAction
     */
    protected function createAction(LoggerInterface $logger, EndpointResolverInterface $endpointResolver)
    {
        return new ResolveEndpointIdpSsoAction($logger, $endpointResolver);
    }
}
