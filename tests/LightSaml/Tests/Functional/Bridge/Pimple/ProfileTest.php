<?php

namespace LightSaml\Tests\Functional\Bridge\Pimple;

use LightSaml\Bridge\Pimple\Container\BuildContainer;
use LightSaml\Bridge\Pimple\Container\Factory\PartyContainerProvider;
use LightSaml\Bridge\Pimple\Container\PartyContainer;
use LightSaml\Bridge\Pimple\Container\StoreContainer;
use LightSaml\Bridge\Pimple\Container\SystemContainer;
use LightSaml\Helper;
use LightSaml\Model\Protocol\Response;
use LightSaml\Provider\TimeProvider\TimeProviderInterface;
use LightSaml\SamlConstants;
use LightSaml\State\Request\RequestState;
use LightSaml\Store\Request\RequestStateArrayStore;
use LightSaml\Tests\Fixtures\Meta\TimeProviderMock;
use Pimple\Container;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

class ProfileTest extends \PHPUnit_Framework_TestCase
{
    const OWN_ENTITY_ID = 'http://localhost/lightSAML/lightSAML';

    public function test_idp_stores()
    {
        $buildContainer = $this->getBuildContainer();
        $allIdpEntityDescriptors = $buildContainer->getPartyContainer()->getIdpEntityDescriptorStore()->all();

        $this->assertCount(4, $allIdpEntityDescriptors);
        $this->assertEquals('https://idp.testshib.org/idp/shibboleth', $allIdpEntityDescriptors[0]->getEntityID());
        $this->assertEquals('https://sp.testshib.org/shibboleth-sp', $allIdpEntityDescriptors[1]->getEntityID());
        $this->assertEquals('http://localhost/lightSAML/lightSAML-IDP/web/idp', $allIdpEntityDescriptors[2]->getEntityID());
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
        $this->assertEquals('http://localhost/lightsaml/lightSAML/web/sp/acs.php', $root->SPSSODescriptor->AssertionConsumerService['Location']);
    }

    public function test_send_authn_request_profile()
    {
        $buildContainer = $this->getBuildContainer();

        $idpEntityId = 'http://localhost/lightSAML/lightSAML-IDP/web/idp';

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
        $this->assertEquals(self::OWN_ENTITY_ID, (string)$root->children('saml', true)->Issuer);
        $this->assertEquals('https://localhost/lightsaml/lightSAML-IDP/web/idp/login.php', $root['Destination']);
    }

    public function test_receive_response_profile()
    {
        $buildContainer = $this->getBuildContainer(
            '_641f9c4cc9138cee3fb1ff31dc55c0df113f13646f',
            new TimeProviderMock(
                new \DateTime('@'.Helper::parseSAMLTime('2015-11-20T09:04:20Z'), new \DateTimeZone('UTC'))
            )
        );

        $builder = new \LightSaml\Builder\Profile\WebBrowserSso\Sp\SsoSpReceiveResponseProfileBuilder($buildContainer);

        $context = $builder->buildContext();
        $action = $builder->buildAction();

        $request = Request::create('http://localhost/lightsaml/lightSAML/web/sp/acs.php', 'POST', ['SAMLResponse'=>$this->getSamlResponseCode()]);
        $context->getHttpRequestContext()->setRequest($request);

        $action->execute($context);

        /** @var Response $response */
        $response = $context->getInboundMessage();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertCount(1, $response->getAllAssertions());
        $this->assertEquals('tmilos', $response->getFirstAssertion()->getFirstAttributeStatement()->getFirstAttributeByName('uid')->getFirstAttributeValue());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlBuildException
     * @expectedExceptionMessage Attribute value provider not set
     */
    public function test_attribute_value_provider_throws_exception()
    {
        $buildContainer = $this->getBuildContainer();
        $buildContainer->getProviderContainer()->getAttributeValueProvider();
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlBuildException
     * @expectedExceptionMessage Attribute name provider not set
     */
    public function test_attribute_name_provider_throws_exception()
    {
        $buildContainer = $this->getBuildContainer();
        $buildContainer->getProviderContainer()->getAttributeNameProvider();
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlBuildException
     * @expectedExceptionMessage Session info provider not set
     */
    public function test_session_info_provider_throws_exception()
    {
        $buildContainer = $this->getBuildContainer();
        $buildContainer->getProviderContainer()->getSessionInfoProvider();
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlBuildException
     * @expectedExceptionMessage Name ID provider not set
     */
    public function test_name_id_provider_throws_exception()
    {
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
            'http://localhost/lightsaml/lightSAML/web/sp/acs.php',
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
        return "PHNhbWxwOlJlc3BvbnNlIHhtbG5zOnNhbWxwPSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6cHJvdG9jb2wiIHhtbG5zOnNh
bWw9InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjIuMDphc3NlcnRpb24iIElEPSJfNjBjZGNkNTBlNTA2YjdlYjYxNzNiYjc1YmEy
NzUwZGFmYzgzZmI2OGYyIiBWZXJzaW9uPSIyLjAiIElzc3VlSW5zdGFudD0iMjAxNS0xMS0yMFQwOTowNDozMloiIERlc3RpbmF0
aW9uPSJodHRwOi8vbG9jYWxob3N0L2xpZ2h0c2FtbC9saWdodFNBTUwvd2ViL3NwL2Fjcy5waHAiIEluUmVzcG9uc2VUbz0iXzY0MWY5YzRjYzkxMzhjZWUzZmIxZmYzMWRjNTVjMGRmMTEzZjEzNjQ2ZiI
+PHNhbWw6SXNzdWVyPmh0dHBzOi8vb3BlbmlkcC5mZWlkZS5ubzwvc2FtbDpJc3N1ZXI+PGRzOlNpZ25hdHVyZSB4bWxuczpkcz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnIyI
+CiAgPGRzOlNpZ25lZEluZm8+PGRzOkNhbm9uaWNhbGl6YXRpb25NZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8
yMDAxLzEwL3htbC1leGMtYzE0biMiLz4KICAgIDxkczpTaWduYXR1cmVNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9
yZy8yMDAwLzA5L3htbGRzaWcjcnNhLXNoYTEiLz4KICA8ZHM6UmVmZXJlbmNlIFVSST0iI182MGNkY2Q1MGU1MDZiN2ViNjE3M2J
iNzViYTI3NTBkYWZjODNmYjY4ZjIiPjxkczpUcmFuc2Zvcm1zPjxkczpUcmFuc2Zvcm0gQWxnb3JpdGhtPSJodHRwOi8vd3d3Lnc
zLm9yZy8yMDAwLzA5L3htbGRzaWcjZW52ZWxvcGVkLXNpZ25hdHVyZSIvPjxkczpUcmFuc2Zvcm0gQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzEwL3htbC1leGMtYzE0biMiLz48L2RzOlRyYW5zZm9ybXM
+PGRzOkRpZ2VzdE1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyNzaGExIi8+PGRzOkR
pZ2VzdFZhbHVlPllkZXV0eHhEY2lwbGtRczV6bzJYOE5mNzZqST08L2RzOkRpZ2VzdFZhbHVlPjwvZHM6UmVmZXJlbmNlPjwvZHM6U2lnbmVkSW5mbz48ZHM6U2lnbmF0dXJlVmFsdWU
+aDZsTGF1NXIzZU91ajY1cWgxMTRVbk5yb1NYbmxxKy9wN241MTgyUko3bVI0eWVHUjZBVmdRMWljZkZRT1c2NlZhRmdpTjkwODR
EL1NpZjdoS0FYTUlHazB5YzZ6MnY0T3RkZDZmUEh3K0oxNDNSRU91d1pBWGltMVppUjkyOFVKY3VpTS9JV29Ed2JxNWVuWHF2MDN4VERvVFRIWnpkeFhCNUxjYUFxanRRPTwvZHM6U2lnbmF0dXJlVmFsdWU
+CjxkczpLZXlJbmZvPjxkczpYNTA5RGF0YT48ZHM6WDUwOUNlcnRpZmljYXRlPk1JSUNpekNDQWZRQ0NRQ1k4dEthTWMwQk1qQU5
CZ2txaGtpRzl3MEJBUVVGQURDQmlURUxNQWtHQTFVRUJoTUNUazh4RWpBUUJnTlZCQWdUQ1ZSeWIyNWthR1ZwYlRFUU1BNEdBMVV
FQ2hNSFZVNUpUa1ZVVkRFT01Bd0dBMVVFQ3hNRlJtVnBaR1V4R1RBWEJnTlZCQU1URUc5d1pXNXBaSEF1Wm1WcFpHVXVibTh4S1R
BbkJna3Foa2lHOXcwQkNRRVdHbUZ1WkhKbFlYTXVjMjlzWW1WeVowQjFibWx1WlhSMExtNXZNQjRYRFRBNE1EVXdPREE1TWpJME9
Gb1hEVE0xTURreU16QTVNakkwT0Zvd2dZa3hDekFKQmdOVkJBWVRBazVQTVJJd0VBWURWUVFJRXdsVWNtOXVaR2hsYVcweEVEQU9
CZ05WQkFvVEIxVk9TVTVGVkZReERqQU1CZ05WQkFzVEJVWmxhV1JsTVJrd0Z3WURWUVFERXhCdmNHVnVhV1J3TG1abGFXUmxMbTV
2TVNrd0p3WUpLb1pJaHZjTkFRa0JGaHBoYm1SeVpXRnpMbk52YkdKbGNtZEFkVzVwYm1WMGRDNXViekNCbnpBTkJna3Foa2lHOXc
wQkFRRUZBQU9CalFBd2dZa0NnWUVBdDhqTG9xSTFWVGx4QVoyYXhpRElUaFdjQU9YZHU4S2tWVVdhTi9Tb29POU8wUVE3S1JValN
HS045Sks2NUFGUkRYUWtXUEF1NEhsbk80bm9ZbEZTTG5ZeUR4STY2TENyNzF4NGxnRkpqcUxlQXZCL0dxQnFGZklaM1lLL05yaG5
VcUZ3WnU2M25MclpqY1VaeE5hUGpPT1NSU0RhWHB2MWtiNWszak9pU0dFQ0F3RUFBVEFOQmdrcWhraUc5dzBCQVFVRkFBT0JnUUJ
RWWo0Y0FhZldhWWZqQlUyemkxRWx3U3RJYUo1bnlwL3MvOEI4U0FQSzJUNzlNY015Y2NQM3dTVzEzTEhrbU0xandLZTNBQ0ZYQnZ
xR1FOMEliY0g0OWh1MEZLaFlGTS9HUERKY0lIRkJzaXlNQlhDaHB5ZTl2QmFUTkVCQ3RVM0tqanlHMGhSVDJtQVE5aCtia1BtT3ZsRW8vYUgweFI2OFo5aHc0UEYxM3c9PTwvZHM6WDUwOUNlcnRpZmljYXRlPjwvZHM6WDUwOURhdGE
+PC9kczpLZXlJbmZvPjwvZHM6U2lnbmF0dXJlPjxzYW1scDpTdGF0dXM+PHNhbWxwOlN0YXR1c0NvZGUgVmFsdWU9InVybjpvYXN
pczpuYW1lczp0YzpTQU1MOjIuMDpzdGF0dXM6U3VjY2VzcyIvPjwvc2FtbHA6U3RhdHVzPjxzYW1sOkFzc2VydGlvbiB4bWxuczp
4c2k9Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvWE1MU2NoZW1hLWluc3RhbmNlIiB4bWxuczp4cz0iaHR0cDovL3d3dy53My5vcmc
vMjAwMS9YTUxTY2hlbWEiIElEPSJfOTdhNDIwZWE1ODU2YmUzNTA3NTY2Y2M1ZTVjZTAyZjJhYTA0NDk0MTJmIiBWZXJzaW9uPSI
yLjAiIElzc3VlSW5zdGFudD0iMjAxNS0xMS0yMFQwOTowNDozMloiPjxzYW1sOklzc3Vlcj5odHRwczovL29wZW5pZHAuZmVpZGU
ubm88L3NhbWw6SXNzdWVyPjxkczpTaWduYXR1cmUgeG1sbnM6ZHM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyM
iPgogIDxkczpTaWduZWRJbmZvPjxkczpDYW5vbmljYWxpemF0aW9uTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS8xMC94bWwtZXhjLWMxNG4jIi8
+CiAgICA8ZHM6U2lnbmF0dXJlTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI3JzYS1zaGExIi8
+CiAgPGRzOlJlZmVyZW5jZSBVUkk9IiNfOTdhNDIwZWE1ODU2YmUzNTA3NTY2Y2M1ZTVjZTAyZjJhYTA0NDk0MTJmIj48ZHM6VHJ
hbnNmb3Jtcz48ZHM6VHJhbnNmb3JtIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI2VudmVsb3B
lZC1zaWduYXR1cmUiLz48ZHM6VHJhbnNmb3JtIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS8xMC94bWwtZXhjLWMxNG4jIi8
+PC9kczpUcmFuc2Zvcm1zPjxkczpEaWdlc3RNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGR
zaWcjc2hhMSIvPjxkczpEaWdlc3RWYWx1ZT5lNHk2Y1V1Y0NGZkxrZUtuOG0veStiM09HS2M9PC9kczpEaWdlc3RWYWx1ZT48L2RzOlJlZmVyZW5jZT48L2RzOlNpZ25lZEluZm8
+PGRzOlNpZ25hdHVyZVZhbHVlPmdWVVArUVVXbmlvL1BLYm55TkhRWFNFR1dtSW51bTZPaFZISTArc2dNcWVaUUEyNXhUdnRMdkJ
qSUhIdWRKcjhWcDFMdUJiNnZRdmZDVlBqOWNPSDZDVGxJREZXTnAxV2tycU9JVGdrUk5rb1J4TlZGTkhOOG8vdDRnWFNlbXl5Umt
KWVorckFrQTRIZVhmcEcrNUlWMndmMDlwcHkxcFVrc1JRYjI1Sm9mbz08L2RzOlNpZ25hdHVyZVZhbHVlPgo8ZHM6S2V5SW5mbz48ZHM6WDUwOURhdGE
+PGRzOlg1MDlDZXJ0aWZpY2F0ZT5NSUlDaXpDQ0FmUUNDUUNZOHRLYU1jMEJNakFOQmdrcWhraUc5dzBCQVFVRkFEQ0JpVEVMTUF
rR0ExVUVCaE1DVGs4eEVqQVFCZ05WQkFnVENWUnliMjVrYUdWcGJURVFNQTRHQTFVRUNoTUhWVTVKVGtWVVZERU9NQXdHQTFVRUN
4TUZSbVZwWkdVeEdUQVhCZ05WQkFNVEVHOXdaVzVwWkhBdVptVnBaR1V1Ym04eEtUQW5CZ2txaGtpRzl3MEJDUUVXR21GdVpISmx
ZWE11YzI5c1ltVnlaMEIxYm1sdVpYUjBMbTV2TUI0WERUQTRNRFV3T0RBNU1qSTBPRm9YRFRNMU1Ea3lNekE1TWpJME9Gb3dnWWt
4Q3pBSkJnTlZCQVlUQWs1UE1SSXdFQVlEVlFRSUV3bFVjbTl1WkdobGFXMHhFREFPQmdOVkJBb1RCMVZPU1U1RlZGUXhEakFNQmd
OVkJBc1RCVVpsYVdSbE1Sa3dGd1lEVlFRREV4QnZjR1Z1YVdSd0xtWmxhV1JsTG01dk1Ta3dKd1lKS29aSWh2Y05BUWtCRmhwaGJ
tUnlaV0Z6TG5OdmJHSmxjbWRBZFc1cGJtVjBkQzV1YnpDQm56QU5CZ2txaGtpRzl3MEJBUUVGQUFPQmpRQXdnWWtDZ1lFQXQ4akx
vcUkxVlRseEFaMmF4aURJVGhXY0FPWGR1OEtrVlVXYU4vU29vTzlPMFFRN0tSVWpTR0tOOUpLNjVBRlJEWFFrV1BBdTRIbG5PNG5
vWWxGU0xuWXlEeEk2NkxDcjcxeDRsZ0ZKanFMZUF2Qi9HcUJxRmZJWjNZSy9OcmhuVXFGd1p1NjNuTHJaamNVWnhOYVBqT09TUlN
EYVhwdjFrYjVrM2pPaVNHRUNBd0VBQVRBTkJna3Foa2lHOXcwQkFRVUZBQU9CZ1FCUVlqNGNBYWZXYVlmakJVMnppMUVsd1N0SWF
KNW55cC9zLzhCOFNBUEsyVDc5TWNNeWNjUDN3U1cxM0xIa21NMWp3S2UzQUNGWEJ2cUdRTjBJYmNINDlodTBGS2hZRk0vR1BESmN
JSEZCc2l5TUJYQ2hweWU5dkJhVE5FQkN0VTNLamp5RzBoUlQybUFROWgrYmtQbU92bEVvL2FIMHhSNjhaOWh3NFBGMTN3PT08L2R
zOlg1MDlDZXJ0aWZpY2F0ZT48L2RzOlg1MDlEYXRhPjwvZHM6S2V5SW5mbz48L2RzOlNpZ25hdHVyZT48c2FtbDpTdWJqZWN0Pjx
zYW1sOk5hbWVJRCBTUE5hbWVRdWFsaWZpZXI9Imh0dHA6Ly9sb2NhbGhvc3QvbGlnaHRTQU1ML2xpZ2h0U0FNTCIgRm9ybWF0PSJ
1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6bmFtZWlkLWZvcm1hdDp0cmFuc2llbnQiPl8yY2YxYTJhYjJmNzc1MThmNWQ2MDE
3NjZlMzJjYjNjMWZmZWRhYTFiN2Y8L3NhbWw6TmFtZUlEPjxzYW1sOlN1YmplY3RDb25maXJtYXRpb24gTWV0aG9kPSJ1cm46b2F
zaXM6bmFtZXM6dGM6U0FNTDoyLjA6Y206YmVhcmVyIj48c2FtbDpTdWJqZWN0Q29uZmlybWF0aW9uRGF0YSBOb3RPbk9yQWZ0ZXI
9IjIwMTUtMTEtMjBUMDk6MDk6MzJaIiBSZWNpcGllbnQ9Imh0dHA6Ly9sb2NhbGhvc3QvbGlnaHRzYW1sL2xpZ2h0U0FNTC93ZWIvc3AvYWNzLnBocCIgSW5SZXNwb25zZVRvPSJfNjQxZjljNGNjOTEzOGNlZTNmYjFmZjMxZGM1NWMwZGYxMTNmMTM2NDZmIi8
+PC9zYW1sOlN1YmplY3RDb25maXJtYXRpb24+PC9zYW1sOlN1YmplY3Q+PHNhbWw6Q29uZGl0aW9ucyBOb3RCZWZvcmU9IjIwMTU
tMTEtMjBUMDk6MDQ6MDJaIiBOb3RPbk9yQWZ0ZXI9IjIwMTUtMTEtMjBUMDk6MDk6MzJaIj48c2FtbDpBdWRpZW5jZVJlc3RyaWN
0aW9uPjxzYW1sOkF1ZGllbmNlPmh0dHA6Ly9sb2NhbGhvc3QvbGlnaHRTQU1ML2xpZ2h0U0FNTDwvc2FtbDpBdWRpZW5jZT48L3N
hbWw6QXVkaWVuY2VSZXN0cmljdGlvbj48L3NhbWw6Q29uZGl0aW9ucz48c2FtbDpBdXRoblN0YXRlbWVudCBBdXRobkluc3RhbnQ
9IjIwMTUtMTEtMjBUMDk6MDA6MzFaIiBTZXNzaW9uTm90T25PckFmdGVyPSIyMDE1LTExLTIwVDE3OjA0OjMyWiIgU2Vzc2lvbkluZGV4PSJfZGQwZWI5N2ZkMzM1YzdhYzYzODExN2JjN2FhZjUxMWM3N2NjYWNmMTlkIj48c2FtbDpBdXRobkNvbnRleHQ
+PHNhbWw6QXV0aG5Db250ZXh0Q2xhc3NSZWY+dXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOmFjOmNsYXNzZXM6UGFzc3dvcmQ8L3NhbWw6QXV0aG5Db250ZXh0Q2xhc3NSZWY
+PC9zYW1sOkF1dGhuQ29udGV4dD48L3NhbWw6QXV0aG5TdGF0ZW1lbnQ+PHNhbWw6QXR0cmlidXRlU3RhdGVtZW50PjxzYW1sOkF
0dHJpYnV0ZSBOYW1lPSJ1aWQiIE5hbWVGb3JtYXQ9InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjIuMDphdHRybmFtZS1mb3JtYXQ
6dXJpIj48c2FtbDpBdHRyaWJ1dGVWYWx1ZSB4c2k6dHlwZT0ieHM6c3RyaW5nIj50bWlsb3M8L3NhbWw6QXR0cmlidXRlVmFsdWU
+PC9zYW1sOkF0dHJpYnV0ZT48c2FtbDpBdHRyaWJ1dGUgTmFtZT0iZ2l2ZW5OYW1lIiBOYW1lRm9ybWF0PSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6YXR0cm5hbWUtZm9ybWF0OnVyaSI
+PHNhbWw6QXR0cmlidXRlVmFsdWUgeHNpOnR5cGU9InhzOnN0cmluZyI+TWlsb3M8L3NhbWw6QXR0cmlidXRlVmFsdWU+PC9zYW1
sOkF0dHJpYnV0ZT48c2FtbDpBdHRyaWJ1dGUgTmFtZT0ic24iIE5hbWVGb3JtYXQ9InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjI
uMDphdHRybmFtZS1mb3JtYXQ6dXJpIj48c2FtbDpBdHRyaWJ1dGVWYWx1ZSB4c2k6dHlwZT0ieHM6c3RyaW5nIj5Ub21pYzwvc2F
tbDpBdHRyaWJ1dGVWYWx1ZT48L3NhbWw6QXR0cmlidXRlPjxzYW1sOkF0dHJpYnV0ZSBOYW1lPSJjbiIgTmFtZUZvcm1hdD0idXJ
uOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOmF0dHJuYW1lLWZvcm1hdDp1cmkiPjxzYW1sOkF0dHJpYnV0ZVZhbHVlIHhzaTp0eXBlPSJ4czpzdHJpbmciPk1pbG9zIFRvbWljPC9zYW1sOkF0dHJpYnV0ZVZhbHVlPjwvc2FtbDpBdHRyaWJ1dGU
+PHNhbWw6QXR0cmlidXRlIE5hbWU9Im1haWwiIE5hbWVGb3JtYXQ9InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjIuMDphdHRybmF
tZS1mb3JtYXQ6dXJpIj48c2FtbDpBdHRyaWJ1dGVWYWx1ZSB4c2k6dHlwZT0ieHM6c3RyaW5nIj50bWlsb3NAZ21haWwuY29tPC9zYW1sOkF0dHJpYnV0ZVZhbHVlPjwvc2FtbDpBdHRyaWJ1dGU
+PHNhbWw6QXR0cmlidXRlIE5hbWU9ImVkdVBlcnNvblByaW5jaXBhbE5hbWUiIE5hbWVGb3JtYXQ9InVybjpvYXNpczpuYW1lczp
0YzpTQU1MOjIuMDphdHRybmFtZS1mb3JtYXQ6dXJpIj48c2FtbDpBdHRyaWJ1dGVWYWx1ZSB4c2k6dHlwZT0ieHM6c3RyaW5nIj50bWlsb3NAcm5kLmZlaWRlLm5vPC9zYW1sOkF0dHJpYnV0ZVZhbHVlPjwvc2FtbDpBdHRyaWJ1dGU
+PHNhbWw6QXR0cmlidXRlIE5hbWU9ImVkdVBlcnNvblRhcmdldGVkSUQiIE5hbWVGb3JtYXQ9InVybjpvYXNpczpuYW1lczp0Yzp
TQU1MOjIuMDphdHRybmFtZS1mb3JtYXQ6dXJpIj48c2FtbDpBdHRyaWJ1dGVWYWx1ZSB4c2k6dHlwZT0ieHM6c3RyaW5nIj4xMzY
xODJjMDQ4Mjk5MjdlZTllZmUyY2YyNzQ5OWU1MGZjYzNhNTE5PC9zYW1sOkF0dHJpYnV0ZVZhbHVlPjwvc2FtbDpBdHRyaWJ1dGU
+PHNhbWw6QXR0cmlidXRlIE5hbWU9InVybjpvaWQ6MC45LjIzNDIuMTkyMDAzMDAuMTAwLjEuMSIgTmFtZUZvcm1hdD0idXJuOm9
hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOmF0dHJuYW1lLWZvcm1hdDp1cmkiPjxzYW1sOkF0dHJpYnV0ZVZhbHVlIHhzaTp0eXBlPSJ
4czpzdHJpbmciPnRtaWxvczwvc2FtbDpBdHRyaWJ1dGVWYWx1ZT48L3NhbWw6QXR0cmlidXRlPjxzYW1sOkF0dHJpYnV0ZSBOYW1
lPSJ1cm46b2lkOjIuNS40LjQyIiBOYW1lRm9ybWF0PSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6YXR0cm5hbWUtZm9ybWF0OnVyaSI
+PHNhbWw6QXR0cmlidXRlVmFsdWUgeHNpOnR5cGU9InhzOnN0cmluZyI+TWlsb3M8L3NhbWw6QXR0cmlidXRlVmFsdWU+PC9zYW1
sOkF0dHJpYnV0ZT48c2FtbDpBdHRyaWJ1dGUgTmFtZT0idXJuOm9pZDoyLjUuNC40IiBOYW1lRm9ybWF0PSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6YXR0cm5hbWUtZm9ybWF0OnVyaSI
+PHNhbWw6QXR0cmlidXRlVmFsdWUgeHNpOnR5cGU9InhzOnN0cmluZyI+VG9taWM8L3NhbWw6QXR0cmlidXRlVmFsdWU+PC9zYW1
sOkF0dHJpYnV0ZT48c2FtbDpBdHRyaWJ1dGUgTmFtZT0idXJuOm9pZDoyLjUuNC4zIiBOYW1lRm9ybWF0PSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6YXR0cm5hbWUtZm9ybWF0OnVyaSI
+PHNhbWw6QXR0cmlidXRlVmFsdWUgeHNpOnR5cGU9InhzOnN0cmluZyI+TWlsb3MgVG9taWM8L3NhbWw6QXR0cmlidXRlVmFsdWU
+PC9zYW1sOkF0dHJpYnV0ZT48c2FtbDpBdHRyaWJ1dGUgTmFtZT0idXJuOm9pZDowLjkuMjM0Mi4xOTIwMDMwMC4xMDAuMS4zIiBOYW1lRm9ybWF0PSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6YXR0cm5hbWUtZm9ybWF0OnVyaSI
+PHNhbWw6QXR0cmlidXRlVmFsdWUgeHNpOnR5cGU9InhzOnN0cmluZyI+dG1pbG9zQGdtYWlsLmNvbTwvc2FtbDpBdHRyaWJ1dGV
WYWx1ZT48L3NhbWw6QXR0cmlidXRlPjxzYW1sOkF0dHJpYnV0ZSBOYW1lPSJ1cm46b2lkOjEuMy42LjEuNC4xLjU5MjMuMS4xLjE
uNiIgTmFtZUZvcm1hdD0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOmF0dHJuYW1lLWZvcm1hdDp1cmkiPjxzYW1sOkF0dHJpYnV0ZVZhbHVlIHhzaTp0eXBlPSJ4czpzdHJpbmciPnRtaWxvc0BybmQuZmVpZGUubm88L3NhbWw6QXR0cmlidXRlVmFsdWU
+PC9zYW1sOkF0dHJpYnV0ZT48c2FtbDpBdHRyaWJ1dGUgTmFtZT0idXJuOm9pZDoxLjMuNi4xLjQuMS41OTIzLjEuMS4xLjEwIiBOYW1lRm9ybWF0PSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6YXR0cm5hbWUtZm9ybWF0OnVyaSI
+PHNhbWw6QXR0cmlidXRlVmFsdWUgeHNpOnR5cGU9InhzOnN0cmluZyI+MTM2MTgyYzA0ODI5OTI3ZWU5ZWZlMmNmMjc0OTllNTBmY2MzYTUxOTwvc2FtbDpBdHRyaWJ1dGVWYWx1ZT48L3NhbWw6QXR0cmlidXRlPjwvc2FtbDpBdHRyaWJ1dGVTdGF0ZW1lbnQ
+PC9zYW1sOkFzc2VydGlvbj48L3NhbWxwOlJlc3BvbnNlPg==";
    }
}
