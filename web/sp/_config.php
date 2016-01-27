<?php

require_once __DIR__.'/../../vendor/autoload.php';

class SpConfig
{
    const OWN_ENTITY_ID = 'https://localhost/lightSAML/lightSAML';

    /** @var  \SpConfig */
    private static $instance;

    public $debug = true;

    /**
     * @return \SpConfig
     */
    public static function current()
    {
        if (null == self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @return \LightSaml\Build\Container\BuildContainerInterface
     */
    public function getBuildContainer()
    {
        $result = new \LightSaml\Bridge\Pimple\Container\BuildContainer(new \Pimple\Container());
        $this->buildOwnContext($result);
        $this->buildSystemContext($result);
        $this->buildPartyContext($result);
        $this->buildStoreContext($result);
        $this->buildProviderContext($result);
        $this->buildCredentialContext($result);
        $this->buildServiceContext($result);

        return $result;
    }

    private function buildOwnContext(\LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer)
    {
        $ownCredential = $this->buildOwnCredential();
        $ownEntityDescriptorProvider = $this->buildOwnEntityDescriptorProvider($ownCredential->getCertificate());

        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\OwnContainerProvider(
                $ownEntityDescriptorProvider,
                [$ownCredential]
            )
        );
    }

    private function buildSystemContext(\LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer)
    {
        $buildContainer->getPimple()->register(new \LightSaml\Bridge\Pimple\Container\Factory\SystemContainerProvider());

        $pimple = $buildContainer->getPimple();
        $pimple[\LightSaml\Bridge\Pimple\Container\SystemContainer::LOGGER] = function () {
            return $this->buildLogger();

        };
        $pimple[\LightSaml\Bridge\Pimple\Container\SystemContainer::SESSION] = function () {
            return $this->buildSession();

        };
    }

    private function buildPartyContext(\LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer)
    {
        $buildContainer->getPimple()->register(new \LightSaml\Bridge\Pimple\Container\Factory\PartyContainerProvider());

        $pimple = $buildContainer->getPimple();
        $pimple[\LightSaml\Bridge\Pimple\Container\PartyContainer::IDP_ENTITY_DESCRIPTOR] = function () {
            return $this->buildIdpEntityStore();
        };
        $pimple[\LightSaml\Bridge\Pimple\Container\PartyContainer::TRUST_OPTIONS_STORE] = function () {
            $trustOptions = new \LightSaml\Meta\TrustOptions\TrustOptions();
            $trustOptions->setSignAuthnRequest(true);

            return new \LightSaml\Store\TrustOptions\FixedTrustOptionsStore($trustOptions);
        };
    }

    private function buildStoreContext(\LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer)
    {
        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\StoreContainerProvider(
                $buildContainer->getSystemContainer()
            )
        );
    }

    private function buildProviderContext(\LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer)
    {
        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\ProviderContainerProvider()
        );
    }

    private function buildCredentialContext(\LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer)
    {
        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\CredentialContainerProvider(
                $buildContainer->getPartyContainer(),
                $buildContainer->getOwnContainer()
            )
        );
    }

    private function buildServiceContext(\LightSaml\Bridge\Pimple\Container\BuildContainer $buildContainer)
    {
        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\ServiceContainerProvider(
                $buildContainer->getCredentialContainer(),
                $buildContainer->getStoreContainer(),
                $buildContainer->getSystemContainer()
            )
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    private function buildSession()
    {
        $session = new \Symfony\Component\HttpFoundation\Session\Session();
        $session->setName('PHPSIDSP');
        $session->start();

        return $session;
    }

    /**
     * @return \LightSaml\Credential\X509Credential
     */
    private function buildOwnCredential()
    {
        $ownCredential = new \LightSaml\Credential\X509Credential(
            (new \LightSaml\Credential\X509Certificate())
                ->loadPem(file_get_contents(__DIR__.'/saml.crt')),
            \LightSaml\Credential\KeyHelper::createPrivateKey(__DIR__.'/saml.key', null, true)
        );
        $ownCredential
            ->setEntityId(self::OWN_ENTITY_ID)
        ;

        return $ownCredential;
    }

    /**
     * @param \LightSaml\Credential\X509Certificate $certificate
     *
     * @return \LightSaml\Provider\EntityDescriptor\EntityDescriptorProviderInterface
     */
    private function buildOwnEntityDescriptorProvider(\LightSaml\Credential\X509Certificate $certificate)
    {
        return new \LightSaml\Builder\EntityDescriptor\SimpleEntityDescriptorBuilder(
            self::OWN_ENTITY_ID,
            'https://localhost/lightsaml/lightSAML/web/sp/acs.php',
            null,
            $certificate
        );
    }

    /**
     * @return \LightSaml\Store\EntityDescriptor\FixedEntityDescriptorStore
     */
    private function buildIdpEntityStore()
    {
        $idpProvider = new \LightSaml\Store\EntityDescriptor\FixedEntityDescriptorStore();
        $idpProvider->add(
            \LightSaml\Model\Metadata\EntitiesDescriptor::load(__DIR__.'/testshib-providers.xml')
        );
        $idpProvider->add(
            \LightSaml\Model\Metadata\EntityDescriptor::load(__DIR__.'/localhost-lightsaml-lightsaml-idp.xml')
        );
        $idpProvider->add(
            \LightSaml\Model\Metadata\EntityDescriptor::load(__DIR__.'/openidp.feide.no.xml')
        );
        $idpProvider->add(
            \LightSaml\Model\Metadata\EntityDescriptor::load(__DIR__.'/FederationMetadata.xml')
        );

        return $idpProvider;
    }

    /**
     * @return \Monolog\Logger
     */
    private function buildLogger()
    {
        $logger = new \Monolog\Logger('lightsaml', array(new \Monolog\Handler\StreamHandler(__DIR__.'/sp.log')));

        return $logger;
    }
}
