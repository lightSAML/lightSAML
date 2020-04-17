<?php

namespace LightSaml\Tests\Functional\Bridge\Pimple;

use LightSaml\Bridge\Pimple\Container\BuildContainer;
use LightSaml\Bridge\Pimple\Container\PartyContainer;
use LightSaml\Bridge\Pimple\Container\StoreContainer;
use LightSaml\Bridge\Pimple\Container\SystemContainer;
use LightSaml\ClaimTypes;
use LightSaml\Helper;
use LightSaml\Model\Protocol\Response;
use LightSaml\Provider\TimeProvider\TimeProviderInterface;
use LightSaml\SamlConstants;
use LightSaml\State\Request\RequestState;
use LightSaml\Store\Request\RequestStateArrayStore;
use LightSaml\Tests\BaseTestCase;
use LightSaml\Tests\Fixtures\Meta\TimeProviderMock;
use Pimple\Container;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

class ProfileTest extends BaseTestCase
{
    const OWN_ENTITY_ID = 'https://localhost/lightSAML/lightSAML';

    public function test_idp_stores()
    {
        $buildContainer = $this->getBuildContainer();
        $allIdpEntityDescriptors = $buildContainer->getPartyContainer()->getIdpEntityDescriptorStore()->all();

        $this->assertCount(4, $allIdpEntityDescriptors);
        $this->assertEquals('https://idp.testshib.org/idp/shibboleth', $allIdpEntityDescriptors[0]->getEntityID());
        $this->assertEquals('https://sp.testshib.org/shibboleth-sp', $allIdpEntityDescriptors[1]->getEntityID());
        $this->assertEquals('https://localhost/lightSAML/lightSAML-IDP', $allIdpEntityDescriptors[2]->getEntityID());
        $this->assertEquals('https://openidp.feide.no', $allIdpEntityDescriptors[3]->getEntityID());
    }

    public function test_metadata_profile()
    {
        $buildContainer = $this->getBuildContainer();

        $builder = new \LightSaml\Builder\Profile\Metadata\MetadataProfileBuilder($buildContainer);

        $context = $builder->buildContext();
        $action = $builder->buildAction();

        $action->execute($context);

        $this->assertNotNull($context->getHttpResponseContext()->getResponse());
        $xml = $context->getHttpResponseContext()->getResponse()->getContent();

        $root = new \SimpleXMLElement($xml);

        $this->assertEquals('EntityDescriptor', $root->getName());
        $this->assertEquals('SPSSODescriptor', $root->SPSSODescriptor->getName());
        $this->assertEquals('https://localhost/lightsaml/lightSAML/web/sp/acs.php', $root->SPSSODescriptor->AssertionConsumerService['Location']);
    }

    public function test_send_authn_request_profile()
    {
        $buildContainer = $this->getBuildContainer();

        $idpEntityId = 'https://localhost/lightSAML/lightSAML-IDP';

        $builder = new \LightSaml\Builder\Profile\WebBrowserSso\Sp\SsoSpSendAuthnRequestProfileBuilder($buildContainer, $idpEntityId);
        $context = $builder->buildContext();
        $action = $builder->buildAction();

        $action->execute($context);

        $html = $context->getHttpResponseContext()->getResponse()->getContent();

        $crawler = new Crawler($html);

        $code = $crawler->filter('body form input[name="SAMLRequest"]')->first()->attr('value');
        $xml = base64_decode($code);

        $root = new \SimpleXMLElement($xml);
        $root->registerXPathNamespace('saml', SamlConstants::NS_ASSERTION);
        $this->assertEquals('AuthnRequest', $root->getName());
        $this->assertEquals(self::OWN_ENTITY_ID, (string) $root->children('saml', true)->Issuer);
        $this->assertEquals('https://localhost/lightsaml/lightSAML-IDP/web/idp/login.php', $root['Destination']);
        $this->assertEquals('Signature', $root->children('ds', true)->Signature->getName());
    }

    public function test_receive_response_profile()
    {
        $buildContainer = $this->getBuildContainer(
            '_1db06e4f91d3997b7ed3285a59f77028071db2dc5f',
            new TimeProviderMock(
                new \DateTime('@'.Helper::parseSAMLTime('2015-11-22T15:37:14Z'), new \DateTimeZone('UTC'))
            )
        );

        $builder = new \LightSaml\Builder\Profile\WebBrowserSso\Sp\SsoSpReceiveResponseProfileBuilder($buildContainer);

        $context = $builder->buildContext();
        $action = $builder->buildAction();

        $request = Request::create('https://localhost/lightsaml/lightSAML/web/sp/acs.php', 'POST', ['SAMLResponse' => $this->getSamlResponseCode()]);
        $context->getHttpRequestContext()->setRequest($request);

        $action->execute($context);

        /** @var Response $response */
        $response = $context->getInboundMessage();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertCount(1, $response->getAllAssertions());
        $this->assertEquals('somebody@example.com', $response->getFirstAssertion()->getFirstAttributeStatement()
            ->getFirstAttributeByName(ClaimTypes::EMAIL_ADDRESS)->getFirstAttributeValue());
    }

    public function test_attribute_value_provider_throws_exception()
    {
        $this->expectExceptionMessage("Attribute value provider not set");
        $this->expectException(\LightSaml\Error\LightSamlBuildException::class);
        $buildContainer = $this->getBuildContainer();
        $buildContainer->getProviderContainer()->getAttributeValueProvider();
    }

    public function test_session_info_provider_throws_exception()
    {
        $this->expectExceptionMessage("Session info provider not set");
        $this->expectException(\LightSaml\Error\LightSamlBuildException::class);
        $buildContainer = $this->getBuildContainer();
        $buildContainer->getProviderContainer()->getSessionInfoProvider();
    }

    public function test_name_id_provider_throws_exception()
    {
        $this->expectExceptionMessage("Name ID provider not set");
        $this->expectException(\LightSaml\Error\LightSamlBuildException::class);
        $buildContainer = $this->getBuildContainer();
        $buildContainer->getProviderContainer()->getNameIdProvider();
    }

    public function test_session()
    {
        $buildContainer = $this->getBuildContainer();
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\Session\Session::class, $buildContainer->getSystemContainer()->getSession());
    }

    public function test_idp_entity_descriptor()
    {
        $pimple = new Container();
        $pimple->register(new \LightSaml\Bridge\Pimple\Container\Factory\PartyContainerProvider());
        $buildContainer = new BuildContainer($pimple);

        $this->assertInstanceOf(\LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface::class, $buildContainer->getPartyContainer()->getIdpEntityDescriptorStore());
    }

    public function test_sp_entity_descriptor()
    {
        $pimple = new Container();
        $pimple->register(new \LightSaml\Bridge\Pimple\Container\Factory\PartyContainerProvider());
        $buildContainer = new BuildContainer($pimple);

        $this->assertInstanceOf(\LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface::class, $buildContainer->getPartyContainer()->getSpEntityDescriptorStore());
    }

    private function getBuildContainer($inResponseTo = null, TimeProviderInterface $timeProvider = null)
    {
        $buildContainer = new BuildContainer($pimple = new Container());

        // OWN
        $ownCredential = new \LightSaml\Credential\X509Credential(
            \LightSaml\Credential\X509Certificate::fromFile(__DIR__.'/../../../../../../web/sp/saml.crt'),
            \LightSaml\Credential\KeyHelper::createPrivateKey(__DIR__.'/../../../../../../web/sp/saml.key', null, true)
        );
        $ownCredential->setEntityId(self::OWN_ENTITY_ID);

        $ownEntityDescriptor = new \LightSaml\Builder\EntityDescriptor\SimpleEntityDescriptorBuilder(
            self::OWN_ENTITY_ID,
            'https://localhost/lightsaml/lightSAML/web/sp/acs.php',
            null,
            $ownCredential->getCertificate()
        );

        $buildContainer->getPimple()->register(new \LightSaml\Bridge\Pimple\Container\Factory\OwnContainerProvider(
            $ownEntityDescriptor,
            [$ownCredential]
        ));

        // SYSTEM
        $buildContainer->getPimple()->register(new \LightSaml\Bridge\Pimple\Container\Factory\SystemContainerProvider(true));
        if ($timeProvider) {
            $pimple[SystemContainer::TIME_PROVIDER] = function () use ($timeProvider) {
                return $timeProvider;
            };
        }

        // PARTY
        $buildContainer->getPimple()->register(new \LightSaml\Bridge\Pimple\Container\Factory\PartyContainerProvider());
        $pimple[PartyContainer::IDP_ENTITY_DESCRIPTOR] = function () {
            $idpProvider = new \LightSaml\Store\EntityDescriptor\FixedEntityDescriptorStore();
            $idpProvider->add(
                \LightSaml\Model\Metadata\EntitiesDescriptor::load(__DIR__.'/../../../../../../web/sp/testshib-providers.xml')
            );
            $idpProvider->add(
                \LightSaml\Model\Metadata\EntityDescriptor::load(__DIR__.'/../../../../../../web/sp/localhost-lightsaml-lightsaml-idp.xml')
            );
            $idpProvider->add(
                \LightSaml\Model\Metadata\EntityDescriptor::load(__DIR__.'/../../../../../../web/sp/openidp.feide.no.xml')
            );

            return $idpProvider;
        };

        // STORE
        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\StoreContainerProvider(
                $buildContainer->getSystemContainer()
            )
        );
        if ($inResponseTo) {
            $pimple[StoreContainer::REQUEST_STATE_STORE] = function () use ($inResponseTo) {
                $store = new RequestStateArrayStore();
                $store->set(new RequestState($inResponseTo));

                return $store;
            };
        }

        // PROVIDER
        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\ProviderContainerProvider()
        );

        // CREDENTIAL
        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\CredentialContainerProvider(
                $buildContainer->getPartyContainer(),
                $buildContainer->getOwnContainer()
            )
        );

        // SERVICE
        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\ServiceContainerProvider(
                $buildContainer->getCredentialContainer(),
                $buildContainer->getStoreContainer(),
                $buildContainer->getSystemContainer()
            )
        );

        return $buildContainer;
    }

    /**
     * @return string
     */
    private function getSamlResponseCode()
    {
        return 'PD94bWwgdmVyc2lvbj0iMS4wIj8+CjxzYW1scDpSZXNwb25zZSB4bWxuczpzYW1scD0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOnByb3RvY29sIiBJRD0iX2I4YzNlZjQ0ZmZjZDNjMjc1OWYyYWU0ZTdjZGVjN2YwZGVhNGU0MGRkOCIgSW5SZXNwb25zZVRvPSJfMWRiMDZlNGY5MWQzOTk3YjdlZDMyODVhNTlmNzcwMjgwNzFkYjJkYzVmIiBWZXJzaW9uPSIyLjAiIElzc3VlSW5zdGFudD0iMjAxNS0xMS0yMlQxNTozODoxNFoiIERlc3RpbmF0aW9uPSJodHRwczovL2xvY2FsaG9zdC9saWdodHNhbWwvbGlnaHRTQU1ML3dlYi9zcC9hY3MucGhwIj48c2FtbDpJc3N1ZXIgeG1sbnM6c2FtbD0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOmFzc2VydGlvbiIgRm9ybWF0PSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6bmFtZWlkLWZvcm1hdDplbnRpdHkiPmh0dHBzOi8vbG9jYWxob3N0L2xpZ2h0U0FNTC9saWdodFNBTUwtSURQPC9zYW1sOklzc3Vlcj48ZHM6U2lnbmF0dXJlIHhtbG5zOmRzPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjIj4KICA8ZHM6U2lnbmVkSW5mbz48ZHM6Q2Fub25pY2FsaXphdGlvbk1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvMTAveG1sLWV4Yy1jMTRuIyIvPgogICAgPGRzOlNpZ25hdHVyZU1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyNyc2Etc2hhMSIvPgogIDxkczpSZWZlcmVuY2UgVVJJPSIjX2I4YzNlZjQ0ZmZjZDNjMjc1OWYyYWU0ZTdjZGVjN2YwZGVhNGU0MGRkOCI+PGRzOlRyYW5zZm9ybXM+PGRzOlRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyNlbnZlbG9wZWQtc2lnbmF0dXJlIi8+PGRzOlRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvMTAveG1sLWV4Yy1jMTRuIyIvPjwvZHM6VHJhbnNmb3Jtcz48ZHM6RGlnZXN0TWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI3NoYTEiLz48ZHM6RGlnZXN0VmFsdWU+djZyaEovaUMyY3p4VXdoVHAxeStFOUZiYjlrPTwvZHM6RGlnZXN0VmFsdWU+PC9kczpSZWZlcmVuY2U+PC9kczpTaWduZWRJbmZvPjxkczpTaWduYXR1cmVWYWx1ZT5oTU43VzkrU08wOU1XOVEvWkx1aHlFNTJpZ3NxclRmallxZmJPRGJBbUtVQXFzT2llU2t6NU84cXhjdGR0K2FQTlNmTmdmeW96MCtuT2xvOXNtellISlRnZm1tV1ZGQUtkQjh3TU5XeU9NN3RZbWVqdU1DYm9pWmJORjJjUWNBc1lTazJ4R1JEVW53OWlRNGNIdTQ1S1ZWR01FdnlLblArQXBGWXgvSmdDUDU4MVQ0dU44c21reFp6bG1Wa1N1NSthdG11eEFXQlVWcytqMkk5US9tU09VUllyZWh5MUxqbWQ5bTBqVG00bGpMS3RZNW9FS3Z5TmpVeEthNlJFcThYbVhuSUtiTjJkazN2eThMcWE1SS9DTWNrQU52YmVDemdxNXdacGFiTlY2bStuSVptZnNvME9jdFdsa3dGOFFUSE9yQXRSMUpzamhmT2hBRzRVN2JvWXc9PTwvZHM6U2lnbmF0dXJlVmFsdWU+CjxkczpLZXlJbmZvPjxkczpYNTA5RGF0YT48ZHM6WDUwOUNlcnRpZmljYXRlPk1JSUR5akNDQXJLZ0F3SUJBZ0lKQUpOT0Z1UWQ3MjdjTUEwR0NTcUdTSWIzRFFFQkJRVUFNRXd4Q3pBSkJnTlZCQVlUQWxKVE1SRXdEd1lEVlFRSUV3aENaV3huY21Ga1pURVNNQkFHQTFVRUNoTUpUR2xuYUhSVFFVMU1NUll3RkFZRFZRUURFdzFzYVdkb2RITmhiV3d1WTI5dE1CNFhEVEUxTURreE16RTVNREUwTUZvWERUSTFNRGt4TURFNU1ERTBNRm93VERFTE1Ba0dBMVVFQmhNQ1VsTXhFVEFQQmdOVkJBZ1RDRUpsYkdkeVlXUmxNUkl3RUFZRFZRUUtFd2xNYVdkb2RGTkJUVXd4RmpBVUJnTlZCQU1URFd4cFoyaDBjMkZ0YkM1amIyMHdnZ0VpTUEwR0NTcUdTSWIzRFFFQkFRVUFBNElCRHdBd2dnRUtBb0lCQVFDN3BVS09QTXlFMm9TY0hMUEdKRlRlcEs5ajFIMDNlL3MvV25PTnc4WndZQmFCSVlJUXVYNnVFOGpGUGREMHVRU2FZcE93NWg1VGdxNnhCVjdtMmtQTzUzaHM4Z0VHV1JiQ2RDdHhpOUVNSndJT1lyK2lzRzBOK0R2VjlLeWJKZjZ0cWNNNTBQaUZqVk50Zng4SXViTXBBS0NicXVhcWRMYUhIMHJnUDFoYmduR201WVpreUVLNHM4eHVMVURTNnFMN043YS9lejJaazQ1dTNMM3FGY3VuY1BJNUJUbkpnNmZxbHlwRGhDRE9CSTVMancxMEhtZ1pIUElYek9oRVBWVityWDJpSGhGNFY5dnpFb2VJVUFCWVhRVk5SUk5IcFBkVnNLNmlUVGt5dmJyR0ovdHYzb0ZaaE5PU0wwS3V5K1E5bmxFOWZFRnF5VXlkSjY3dnNYcVpBZ01CQUFHamdhNHdnYXN3SFFZRFZSME9CQllFRkhQVDZFeTFxZ3hNek1JdDJkM09XdXd6ZlBTVU1Id0dBMVVkSXdSMU1IT0FGSFBUNkV5MXFneE16TUl0MmQzT1d1d3pmUFNVb1ZDa1RqQk1NUXN3Q1FZRFZRUUdFd0pTVXpFUk1BOEdBMVVFQ0JNSVFtVnNaM0poWkdVeEVqQVFCZ05WQkFvVENVeHBaMmgwVTBGTlRERVdNQlFHQTFVRUF4TU5iR2xuYUhSellXMXNMbU52YllJSkFKTk9GdVFkNzI3Y01Bd0dBMVVkRXdRRk1BTUJBZjh3RFFZSktvWklodmNOQVFFRkJRQURnZ0VCQUhrSHR3SkJvZU9odnIwNk0wTWlrS2M5OXplNlRxQUd2ZitRa2dGb1Yxc1dHQWgzTktjQVIrWFNsZksrc1FXckhHa2lpYTVoV0tnQVBNTVVia0xQOURGV2tqYksyNDFpc0NaWkQvTHZBMWFuYlYrN1BpZG4rc3daNWRSN3luWDJ2ajBrRlliK1ZzR1BrYXZOY2o4Uk4vRGR1aE4vVG1pNXNRQWxXaGF3MDZVQWVFcVh0RmVMYlRnTGZmQmFqN1BtUjBJWWp2VFpBMFgyRmRSdTBHWFJ4bjd6Z2hqcHZTcTludVdhM3BHYmZkVnRMNkdJa3dZVVBjRHpqcjRPZUdYTm1JWmUvd01Dbno2VkdaWStMVWd6aS80REFDNlYzT2pNdWhkcVMvMitvMStDWEN3TjA4Q0lIUVY2K0FVQmVuRVZhd01zaWFkTEJneDNrRmU1aVhyWVJNQT08L2RzOlg1MDlDZXJ0aWZpY2F0ZT48L2RzOlg1MDlEYXRhPjwvZHM6S2V5SW5mbz48L2RzOlNpZ25hdHVyZT48ZHM6U2lnbmF0dXJlIHhtbG5zOmRzPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjIj4KICA8ZHM6U2lnbmVkSW5mbz48ZHM6Q2Fub25pY2FsaXphdGlvbk1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvMTAveG1sLWV4Yy1jMTRuIyIvPgogICAgPGRzOlNpZ25hdHVyZU1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyNyc2Etc2hhMSIvPgogIDxkczpSZWZlcmVuY2UgVVJJPSIjX2I4YzNlZjQ0ZmZjZDNjMjc1OWYyYWU0ZTdjZGVjN2YwZGVhNGU0MGRkOCI+PGRzOlRyYW5zZm9ybXM+PGRzOlRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyNlbnZlbG9wZWQtc2lnbmF0dXJlIi8+PGRzOlRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvMTAveG1sLWV4Yy1jMTRuIyIvPjwvZHM6VHJhbnNmb3Jtcz48ZHM6RGlnZXN0TWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI3NoYTEiLz48ZHM6RGlnZXN0VmFsdWU+QVREc2tQVGZuRmtFSTNXenJNQ3JDelNCNktvPTwvZHM6RGlnZXN0VmFsdWU+PC9kczpSZWZlcmVuY2U+PC9kczpTaWduZWRJbmZvPjxkczpTaWduYXR1cmVWYWx1ZT5nZTNYeG1ZMWw3M2FuUWs1L2Fzd0JCVENkVkpGa0U1TlBOOVlCbmVvK2lkREhKb2IvMitPcnZyS1B5QlQwZHBSSnBTellVYUYyRllOaU1RMG5jNkpQdDVyMk52c2Q4aVB0VzZXRjRhYWVYZDRmTlFQZXRNL3AzS2l0ejl3b0NzakdJNUNQNy85eUkrVTlUbEo0QWlLdTNMTEdHbjQxbnZxVG9sUVRRa0RDajJvQjZmTVp6TFBOYlFEVFV2SFI0REdMdGt4aU0wSk1IRUdyYVE4NGhzeENuaTBYQ1BNd0w1dnVmOFMrMng2ODc0VDBpbFllZnU4ZnFRanFOK0t1NklyZFBycE9QRG1SUDhGemlyRWFkMXJNZFpSK2dpM3VmVmpxZFZhTWhMLzFPSkhqZnRUQzRORzI2MElib1V6QVowa1BHM2V1T0tIWjUydHdhT1BVdEFqU1E9PTwvZHM6U2lnbmF0dXJlVmFsdWU+CjxkczpLZXlJbmZvPjxkczpYNTA5RGF0YT48ZHM6WDUwOUNlcnRpZmljYXRlPk1JSUR5akNDQXJLZ0F3SUJBZ0lKQUpOT0Z1UWQ3MjdjTUEwR0NTcUdTSWIzRFFFQkJRVUFNRXd4Q3pBSkJnTlZCQVlUQWxKVE1SRXdEd1lEVlFRSUV3aENaV3huY21Ga1pURVNNQkFHQTFVRUNoTUpUR2xuYUhSVFFVMU1NUll3RkFZRFZRUURFdzFzYVdkb2RITmhiV3d1WTI5dE1CNFhEVEUxTURreE16RTVNREUwTUZvWERUSTFNRGt4TURFNU1ERTBNRm93VERFTE1Ba0dBMVVFQmhNQ1VsTXhFVEFQQmdOVkJBZ1RDRUpsYkdkeVlXUmxNUkl3RUFZRFZRUUtFd2xNYVdkb2RGTkJUVXd4RmpBVUJnTlZCQU1URFd4cFoyaDBjMkZ0YkM1amIyMHdnZ0VpTUEwR0NTcUdTSWIzRFFFQkFRVUFBNElCRHdBd2dnRUtBb0lCQVFDN3BVS09QTXlFMm9TY0hMUEdKRlRlcEs5ajFIMDNlL3MvV25PTnc4WndZQmFCSVlJUXVYNnVFOGpGUGREMHVRU2FZcE93NWg1VGdxNnhCVjdtMmtQTzUzaHM4Z0VHV1JiQ2RDdHhpOUVNSndJT1lyK2lzRzBOK0R2VjlLeWJKZjZ0cWNNNTBQaUZqVk50Zng4SXViTXBBS0NicXVhcWRMYUhIMHJnUDFoYmduR201WVpreUVLNHM4eHVMVURTNnFMN043YS9lejJaazQ1dTNMM3FGY3VuY1BJNUJUbkpnNmZxbHlwRGhDRE9CSTVMancxMEhtZ1pIUElYek9oRVBWVityWDJpSGhGNFY5dnpFb2VJVUFCWVhRVk5SUk5IcFBkVnNLNmlUVGt5dmJyR0ovdHYzb0ZaaE5PU0wwS3V5K1E5bmxFOWZFRnF5VXlkSjY3dnNYcVpBZ01CQUFHamdhNHdnYXN3SFFZRFZSME9CQllFRkhQVDZFeTFxZ3hNek1JdDJkM09XdXd6ZlBTVU1Id0dBMVVkSXdSMU1IT0FGSFBUNkV5MXFneE16TUl0MmQzT1d1d3pmUFNVb1ZDa1RqQk1NUXN3Q1FZRFZRUUdFd0pTVXpFUk1BOEdBMVVFQ0JNSVFtVnNaM0poWkdVeEVqQVFCZ05WQkFvVENVeHBaMmgwVTBGTlRERVdNQlFHQTFVRUF4TU5iR2xuYUhSellXMXNMbU52YllJSkFKTk9GdVFkNzI3Y01Bd0dBMVVkRXdRRk1BTUJBZjh3RFFZSktvWklodmNOQVFFRkJRQURnZ0VCQUhrSHR3SkJvZU9odnIwNk0wTWlrS2M5OXplNlRxQUd2ZitRa2dGb1Yxc1dHQWgzTktjQVIrWFNsZksrc1FXckhHa2lpYTVoV0tnQVBNTVVia0xQOURGV2tqYksyNDFpc0NaWkQvTHZBMWFuYlYrN1BpZG4rc3daNWRSN3luWDJ2ajBrRlliK1ZzR1BrYXZOY2o4Uk4vRGR1aE4vVG1pNXNRQWxXaGF3MDZVQWVFcVh0RmVMYlRnTGZmQmFqN1BtUjBJWWp2VFpBMFgyRmRSdTBHWFJ4bjd6Z2hqcHZTcTludVdhM3BHYmZkVnRMNkdJa3dZVVBjRHpqcjRPZUdYTm1JWmUvd01Dbno2VkdaWStMVWd6aS80REFDNlYzT2pNdWhkcVMvMitvMStDWEN3TjA4Q0lIUVY2K0FVQmVuRVZhd01zaWFkTEJneDNrRmU1aVhyWVJNQT08L2RzOlg1MDlDZXJ0aWZpY2F0ZT48L2RzOlg1MDlEYXRhPjwvZHM6S2V5SW5mbz48L2RzOlNpZ25hdHVyZT48c2FtbHA6U3RhdHVzPjxzYW1scDpTdGF0dXNDb2RlIFZhbHVlPSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6c3RhdHVzOlN1Y2Nlc3MiLz48L3NhbWxwOlN0YXR1cz48c2FtbDpFbmNyeXB0ZWRBc3NlcnRpb24geG1sbnM6c2FtbD0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOmFzc2VydGlvbiI+PHhlbmM6RW5jcnlwdGVkRGF0YSB4bWxuczp4ZW5jPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzA0L3htbGVuYyMiIHhtbG5zOmRzaWc9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyMiIFR5cGU9Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvMDQveG1sZW5jI0VsZW1lbnQiPjx4ZW5jOkVuY3J5cHRpb25NZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzA0L3htbGVuYyNhZXMxMjgtY2JjIi8+PGRzaWc6S2V5SW5mbyB4bWxuczpkc2lnPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjIj48eGVuYzpFbmNyeXB0ZWRLZXk+PHhlbmM6RW5jcnlwdGlvbk1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyNyc2Etc2hhMSIvPjx4ZW5jOkNpcGhlckRhdGE+PHhlbmM6Q2lwaGVyVmFsdWU+TFRHVUdjMzkzSHVaeDM0eVBjRjJjRGZnS0o3bDNOdnYxKzh2QWIvSTUxdXZmSWlyeHVqeGNBcUZ3YVl5WXlMekVzbnlCVUJhdUZiYzZhR2hUQlhuaWlieWpiR0NzWGtTdHc2NUJlb0o5alpPZDNyRVJvZ0dEaHBDVktHZTF3UVZLMVIvRTdZTzZDVWt2WHdqWmhxalBwSEIwcGVlUHJDUVZtOUprV0xBOFNBR0pSRzJwVUNJRUNVWE1LUGxqZ1FFKzNQRkQ0dGlBN0tvZ3hTQTVrcldjbmVjOTA1ZWl5eUJlN202UXh3OEZYSXVZSU50U2VQOUU2dXdCd2xFdUdUOVRjSXJhV0dCYXNmRnlRNXkya0huanJYdHljT2RnaXZOUFoyQ1Y1bUNLRGFxUjloT094dXpPUnVpM3d2cDBoV1E1aHAwQ1Y2Z3hVM3ZRdXM0aXdJQytBPT08L3hlbmM6Q2lwaGVyVmFsdWU+PC94ZW5jOkNpcGhlckRhdGE+PC94ZW5jOkVuY3J5cHRlZEtleT48L2RzaWc6S2V5SW5mbz4KICAgPHhlbmM6Q2lwaGVyRGF0YT4KICAgICAgPHhlbmM6Q2lwaGVyVmFsdWU+Nk5ZaXU1aGN6NTBlblBYdUllRCt0MUlMcTFtQm1QZ0Y1ZTJuWW9hR0wrZ0EvcHgwZkNzOWRJci8wejJkY3pZZ1YrUThFK2FydFptUGJtai8zalFjZkswNHlaZFYvL0YrdEdOaFFYM3VKbmFxSVQ5WGE5dENic29MaWQydE1ucTJkZ3psb1N1VWR4K05MY0p5VnJWYUs4ek15N21PVVdtRndQV2U1b0Y1ZEl2N1ZXeEY4aitrVHAzRHNnVmZlS2ZWaG1wUEFFMGJPanhXWUoxVEhZUEttQjRIS3duOW9COERXM1FnQjV1R2hBUzFlMXFFM0pXdFY0c3Y2b1N2Nk9ocE9oaXREN1BTRzNlTStBSVlYa2tEaXBXU01PZGhJT0lSYlg0aHNZKzczemIzclJXZjFYUlFWVlRnZmVReW9lMG5GdzlQZXpsOGpmUnpSSTlTa0toQVVvKzhwbUtKVUIxZTNNOUZlK1g5K3U5Yjc3QmdUTkFIWlhhS1pKNnRmTmxsVG5sbFBvU2ZDalRDZ3BNOXNEUXE3ZlZNd1ltSHdYWklGTmIxazErWU81bFpOZkRvckpkd0FtRHBrVVRwNm9LKzFJOEw3SzZqdVVPRHB6UlRwUkx3RjY0VjlVUW95TnBVRkZnL0FQeTR1MkFGTTNEcEpoaFR3M0RpTWh5c3BkOXFiN1d4dlZnM3F0a1h3cFA2bWlYd3Q1ei83NVBtUitHWnVVcS9TcEhmNXJDTE5SN2RicHU3UHhyTzliV0s5Q1VTZEswWXA4QTI5dnYzaTJBWk9WM2JoMFJMVlhiV0RPL2gvaWx5MnFpWlNJdlA5cjNZamEwTjdmeUZKbExOWVo4RWkvSkpyUldONkc3Q1hUOVMvRjIvN1RDU24vVXZwK0tYcjF1WXhtQmtXU0JkWmFuaFdPdzdLTC9jUnpLZk83YlMrMkN0UjRHYWVNbkJWK3NqRnliUStzUE9iTExoS3AwUEdTMW9CeG91bnpmQ3V6YTlDQnVRelVqQ1VGT25aampraVZaZVloeFJCa05PZ0lWSTkxQ2l2cmxkc2NQZEQ4UWVlSU8xcldyRHVIZzhwRXBoN00zQXh6RmFBSHRlb2ZlQnl6UVkxNjloQTZCTlhma1hGakdyZXZZNDdIc05YSERMRUEvcWxXTFczc3RZcWtvU3NuWFRjYXI2bmlvRkI1T3pHY1N3bXBWcTBBY0k0VTgzQzJ5RG9VSWxocmRRU2x5NWpKSzliZkYxZmxmMFNwaVk0RDdQYXR6QmFaQVB2eGZESElmWVBBSWNUMXNFNWsyNERrU25DSXZjK3RxRkd2SmFTb080UTdXZkh4ZEMrUlJYaWlQaVpMcHdXdlpxdFNhZEFIYXljd08ybDlJRnd2dTB4WFlNdmZXL1REVWQvTlFsMjJpOFdmTkhBOFVrQUgvR2lvZTA4ZXJCZlN4THdqNlFGTmxhZ2NCL25CV0JMN2RFM1JpbXVqOHdtU0JwN3FGNXdJQnYwNEZGblc1WTVSTmxEV2hUa280ZVpZNkV5NHVXYjNsWnN5di9MWWUwT1JPa0tkNE1LckoxaDJaMU9CaTloUldDMFhzdmpFRmFKUVRmYlQyOHJnUStFVWJxOE04U21XR2RXRnIrbVVIQTVGVjl0NlYzNVpGVGdpdFV3TjFpSnBMeUhpS29wZWpjSEU3Q3cvaEI4OGJTN1h2V0xSSEV2NFdVOE9uQ3FWQ20wdzFVTFptQWNpdWJrNjhuOU55VVNpTWdzbnhjT0owOG1rNEpqMFZJUmlpMUc5NzVub0x5UjZRT3Y4dWlEMlFCYjNZRnZKTzJYSk1JSExBZG05b1JQVEVuYlVxb0RXQXcxcGEvTjEza3JsdENRMFcxT21YcmFjcStSNnMzaG9GU3ZEaU0wZ1FpQ284U2RGZE9RUkJNRVlXMk8rdlhwYWt1dmhEMUVNcnBjWjdHUFlBZ3pjdHhER0M3WEhFVmRxNzBsYW84a2lZMHRoWWJEbVhuaEtpNlFqRDNIZlU0NHhkaXZ6S256VHJJMWZRTUJFQmVBV2dDVCtWbFg1NlRyTEg0WFFTSmEvMnBYbXpaVHI3ZFpwQ2dTQ3pwL1pBNXlLYXovTHVwR2I4TFFWTDJMbnJYb3kzU0RSb012d1owMFM2eFJ6ZnY3QVR1TmMvZW1aem8zYXJ1bXlwNGxYU0hiTGFxdGgvdGsvRWpMZ1NUUStpY1ExeGpPU01mWWZpc21maEhaZXhVN1JhbFhjanlRb1dON1liNlFrV1U3WXpna1BrSWJnMFc1SXpGOURaSkZEVE9LNVAwMkJtbVJnTll0SHVucWRRckV5Mi95djNwVlE4VVBqYjB4SWxlcE5DWVpLSlB1NmJqa0N0OXhZU0pLUnpzV1hqZm5TMGJ1ZDlkOHRTdWZOeCtqZzRTVXErYkJTc2pxWUwwQm93MWZDTjVSM3ZnMHdqU3RteVNTYUdNdW1GVzM1UkVxd2xoQzBoZS9BTVVWZTVOMEFkYmlnclE4QzJTaG9xdW5vOUdkVlo0aDdoUWJKbHo3b3JBaEJVV0VhUmRKQ1FOYmZwN2gzOThJaGhCVkFvVEhlZkFGdmVBRS9YSlhvWkxuejUyUmFHZDk3QTRBaVdNQnlYNDFYUkQ1L1M0cTVZVmVUNjRYUUZrNEk5dWNCZ09HaCsybXkrdWhLL1VEMjBvcm9JaThHVzFNenF0aENNbjBTRURVMVJFMFN5Q3NnK3NKMWZVMWFsLzZEZGFEamZGZ3Z6SWZoNmZqT2FSb2NSaEFrS2dxSnF4YnFnTm5tNlhEWDNLbXorWU5TSWtoVVd2T20xYTV1cnRLelh0dkIzOW5DN1A5VDZVTkpobEZ4ZUxBZlZSa1NlTHdjRnk3Z04yUW4rbXNieGhPSXVGdmNLbHNEY210dTNFY3c1RS9UMWVjMU54MFlzT051Sm0vUUNXWGJNc2tTK0h6QkI2bURuemdIWkRwTkFFMGdaTllnZWVDNTNWSjVyNzNydzkybTVqMEZkVEZCd0p5TzFpRWhmZTNvWWhrR1Q1UzBDRXI5aG1mSWxMMDlXS2E1T2w2Qkx5VE9JUitibnJ3alZXMzZsdFk0QjdDK0QxVGdpbFY4MmJacXR1OFhtcDNva2ZlV3lMR0dTcktpQytBekE0NEhBVVN1TVBKbjZmbHgwTTFOODBaZDFqN3R2aXMxaGNLUGQwbXhuSXJqU1dqd2hoMGpjaVdkZk9zdFBPMTA2UGt0dkdjU0ZLajhzaTJLOVVFcHpCU3VTRTEvODNQNkF5RjJ0T0N1YTBBNlVheWZaR2szTVgzdlRHVmoxNXdoZEc2RlpGY21KTnZVc2l5ZjNaZmhnV0VjQ0FLQ2U5OEYyb2tGYjZrSlRUVk1wSUxPdkUxK3dWKzdZTWRSdi9iL3lzTXZMYVQ1MkdMMmd2citUdy9xL2ZNeGpSRUpadGJzUjJrY1dkMWNVUW1lUE5rNFBlVVRRem9DNGxHaVVFTWEwb1RnZE5hVVZmNkwyUGxUbWNLVW5tOFVERno2K3ZGZ3V5N1M2S0lBSlBBTnA5ekRBOXlHWFBac3lyZ1hQdTdaaWttSmxaMnk3OFFRQkdFdFc2c2pITHNZZHNoRU9wRDc5ZHJWajhvMGhzVGFkdzJHZzE3akMyRFRrZCtRZHQxMkZMZW1VVStWRWxBUUhqWGtmRzc0d2xnbVBqOW0xZWIyS0U1bHAzbDVqd3NrdGNzSjJjMm5zZ3lVaFZ0ZjFCd2svV2RXb3lrWFg2Ty84a29COHpJU0dPdzJMZ1l0cm9UUTJvcHI4YXFmTFZUdXZBbjRwR3JNRmlRUUlmNW50YUcxOFc5bTVIclpFVFBKZXkzWW1GUStJcUJhRjkxOVhlanZLZDYyc2EvZHphakFaQ0pTRmxpaCtOYXZ3WHJFaUJxZU03Z05zVU9mMWZTYUwwUFdDRHVrSUt3RXVDaWdXQWo5NytXUWVNc3hFYkJGRk1oTVNRZjB4ekRHcnE4dDA3Y3FhVlJVQ3lnUXpybml0SHhIYmM2RTBTeE1Dais3MjlCZVJ4aVJLSkxKajBXYlo5dGZUdkJ1Q2ZxbVRoaG9xOFRWelJVMko4OUFuZVNuenJpa044c1psaytHQnJ4ajF2cGViRUJ3Y3diMUNIM0pzZmFNblp4eGFROXppMlYvSDdRVC9NODZ6aUdpamxUS0VMWHZ6MytJY0VDVjE5YlYyMjhsRmRabXppTnBxSytneFZzUjE4VC9GaHZRUFRqQWpteVpDaHhjK0JhcEhoSmd3VTh2ZTl5QURIS0FXeUlsVFVqWDUvcWhqaGhia1ozS1hRa0NrMXBudTFCZ1RyWWZOZFpUYW1nclEyMU5jRVF3L2xBZ1M2ZndTTVdJZ3B1djd4Q3RscTFlVS9RSElST3JQcEI2aGpieUMwbnJWci9neW15b0ZzQVlkeHZTWmhXWGxJbWU3SXBXdWNUaGwzOTNNek5LRXdKTjQzWHNxUGM1QVFkN1NUSCtWVm15VUFYd3Rab3VQa2JqMlVYbGNiVThwU3ZhWFBaVUhIaUIwUTEzNU1RMjA3Rnh6UjhSNlZJY1lSMFQvd25sNGFMTlR5SS9ERVl0ZFhwcHJhcytJWmdFMmM2TE1VTjZOd0tLcWVBUWh6L0hvL1hTL2dZd3VDREcrWWpmYThVRCtJeTd6ai9EUDZJWkphK3RrbFVMZTYxTnlWQzFFOERzNUNBVXROZFp3ZnpncUhyT1lqUk9KVFhOTjNSS2U0YmtYQXdLNDcybzJENy95TFBycFVuVUpiYkJ2dThDMDd1KzI4cmhSSHdaUWUyYytOKzBEc3BoSkJwSnZvNmZFSTRaUnk3QVphZWtGZWdlVThCWmZEL1Vzb29PcERvS29KdjBNZDl4TEJjUE1ITTQ1UWhHMHhob0VZb1JFR2dNMFVwWWlpYkp5OHpwU3ZZM0xKbzFiQjN0bTRORnNDTEZzYXFwQnlnKzYyandTNXVBL29RNktJUzVPWFZwQ3c4Q3FTL01nN2xlQ3ZHTWlaQnhLUjFnZTFseURxUGQ2dE1Sd2dwTmpSZ0gzZEEwSTQrY3RDWk9xY2N4NE1XeVhGcy9aYU12ZkVMK1FJUWJkYTRQNEphVEtla1gyN1dSVGFXdnl1V2lxVFYxeUFxaVEvMGduVjIzVDhNY1VGQ003UWxRZUh5cWtIY29EUFA4VUVEYUNmTEdKdXJSRlVvL2YzNlEwVWI4Q1Fyc2xCd2k5cTRTSjFZR3JvcDBpWGlPTFBQM0RSNHF4VklKZTYxTElIS3pjb016c0duQ29nMXlvSW1UM2ZEZEhrZkdxaHh0Y0xaZGRuczRRcmJnSm5oOXUxUlIzU2hUUG4zRHVYNDNXOWNlVDlVY0ltVlpKZExVRHorRHUxZkt0WTlzNk1wZ1ZmM2xrWWowaTg5c0xiYkxTRlhKb0NNUDV3YUJoT2lzV0ZYWUNCc1dRUXd6Nm9ndXRHdlVCRlFWVzd2KzJjT0ZMWGRDWFlxc21uc2JXbEs1RUprU3NKbm5lNUFZRjhNTGt4VkdHWUlIZU1UY05tZHdseFB6K09yTzdzdXk5TXFTRzR6UmxvWUxLMHRjNXlrTjMxeS9lazNsdVhTY1JYZzQ1YVdMd0NYSDJOYTFxSFpRT0RublVPTFJyb3AyQW1qVG9iVlVMR25Ibm52bHZiZXVyY0ZIY2U3R2laTlozSHNDZG1qY0Zob21QWEVwNW5ab3dkYnJ3dVdXRUJqMmVvMFZOWUZZeVVyOUp3ZFFUZ0tUWThVSU5MTWpkWGNxUXV4Yjh3c25XSEdod0w1dzN2WFBDTkJQSXJUcVZMbklXeUlLZHNIbVYvZnlPc0NxNWE5U2NWMXYrVEtJOTNmamltRU43V0NUVE5XUUdhUlkvcHFTK01MZElIb0xpZjJJUkZqOWcwQWhHS0E5NFNzNXgwbDg0ekhUZlc0bDJsNjBiSWtJZmJWOHBTdTRZQlZlZEZicmRsS0MzWllFV2haZGZhYUp2QTNlYjBQSVpCK1hWUjhaa0NqS3JRTWpmMmZQV29obEo1WVF3MDhRQVJYN0h0VWlZaVJNMzRLTFB3SEpqOFc3U1M0SkpTSGo0V05DY3dYYU9YQStYT0c0LzhFMmhNbDVRMXIvRVVsNGFMTk5CSC8zUlpzNklBT0xJOEhlbkpMSFo0Q1A2VE9vbThYNmFXZGVFa3YvTEk1d3V1OTJ3cjZzdnN5S1ZVTHg2U2MxY0FsbXpQaTVlYkdyR2FFQkxYYmptak9QbzRhbUN4SGdCRzF5aTZXV2w3eHNnU3A1RGptZi9sWEFDQmdCbTJidUxHVDVUWkh3ZDl6eDRTaG10MTlRV0MwZGVhemQxWGlKSkU0dlZ4dFY3VEZIekpkSFlvMnM4YXpDR3lDMnBsN05yYXlmZVAxbDRoSlBiRDNSODlab0k2ZXIxT2p4amNoTzVmcEt5djYrakdhWkdSSXJ2aVpsTktVQmxLaHRNanpxSUZYME1LeDhkY0tGdUpKU2VGOHdFR3NZSlkyNm1YY1VIL0RRLytqZEwwbGJ1WFd1VzN0c21zYWRNZXVjTEpocHg2clRDeUJhT0lhMjdpQjBvVEMvdmNoWXBzK05iUGhrNEsxU3ZVVWR0TXEzeXUyUkRtM0JNeGVndnBUcExiaWl3VEgvcnJuamNFR0ZyaFRqVVJyMUNhaVVsdDNsZnR3N0RoVjJxT1VhUUtzb1pYSEVzMStPajNwODJNWmg3V0Zhd1RyMGJTTFVnMmp4dXhxOTRNUzVrNlpXRzhuNXF0eW1DRWhHNUtJOGxxZ1g3Qk5xRjM2aEJsZkNsUkZrcmlHdFN2T01oMVJJTXRIK0VFei9RdHo0aVp3R05GaHlQN21HdTRsTE94Njg0K3RxeXVEbUwzaE9WTUFwb08zU25jbS84RUIwZ1RTR0xSSEx5amozMnAzM3J0K2NaY0tZVCtKVVdDL0wrUHd4UkdWc3FXRm5LbzM3RURyWXNkWE13V2swZ0YvRmZ6WDhqbXZ5SkZWR29GQXA4eXNmTjBlVm1nVjc0eXZVMGFwbjJGUGlvWjNoZnk4eHhVZmRPNkp2S3lRejk2bWNzSHRtYUpvRTRETEJQNktXNnF2ald1Ky9NOS9HUmk2OFdnNGxyTmFtSEIvOUU0MHhLKzJHS1ZiblV6Yll5OW00bGVCYjNrYjhqNFpqSWFQTHZzK0lzQ1VCRGlHazV3d255TFhFeENpZWZCc0hPbktNQ2w3QzVyRzc1Y3FpWkYxVmFhOHc0Q1F0ZytNandvVGR4Q25FODE4Q0dnMjQ2cDJocjYzUHgrVUVHclVKNE9oZ1V5MDdBZWhidlVCUGpvVWxJd3dpUGQ2VjAxTnBXMTFHekkvQ1AvamVZRVhnSVJ6V0hhb09mL08xK3gxLzFoTVI0R0FibCtlRlcvNjQxaTIvZkFFNGJTRVBwbk9wa1BORkRqbUx4MDF6YVhIMUJIQUVVUWlPa3VMTUpuNDNPQ3ZrbGJXeVhHUlNhMkZjMjhMTlVWM1QwMDVCUUcvNFhWR0E9PTwveGVuYzpDaXBoZXJWYWx1ZT4KICAgPC94ZW5jOkNpcGhlckRhdGE+CjwveGVuYzpFbmNyeXB0ZWREYXRhPjwvc2FtbDpFbmNyeXB0ZWRBc3NlcnRpb24+PC9zYW1scDpSZXNwb25zZT4K';
    }
}
