<?php

namespace LightSaml\Tests\Builder\Context;

use LightSaml\Builder\Context\ProfileContextBuilder;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Profile\Profiles;
use LightSaml\Provider\EntityDescriptor\FixedEntityDescriptorProvider;
use Symfony\Component\HttpFoundation\Request;

class ProfileContextBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_without_arguments()
    {
        new ProfileContextBuilder();
    }

    public function getters_setters_provider()
    {
        return [
            [new Request(), 'setRequest', 'getRequest'],
            [new FixedEntityDescriptorProvider(new EntityDescriptor()), 'setOwnEntityDescriptorProvider', 'getOwnEntityDescriptorProvider'],
            [Profiles::METADATA, 'setProfileId', 'getProfileId'],
            [ProfileContext::ROLE_IDP, 'setProfileRole', 'getProfileRole'],
        ];
    }

    /**
     * @dataProvider getters_setters_provider
     */
    public function test_getters_setters($value, $setter, $getter)
    {
        $builder = new ProfileContextBuilder();
        $builder->{$setter}($value);
        $this->assertSame($value, $builder->{$getter}());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlBuildException
     * @expectedExceptionMessage HTTP Request not set
     */
    public function test_build_throws_exception_when_request_not_set()
    {
        $builder = new ProfileContextBuilder();

        $builder->build();
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlBuildException
     * @expectedExceptionMessage Own EntityDescriptor not set
     */
    public function test_build_throws_exception_when_own_entity_descriptor_not_set()
    {
        $builder = new ProfileContextBuilder();
        $builder->setRequest(new Request());

        $builder->build();
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlBuildException
     * @expectedExceptionMessage ProfileID not set
     */
    public function test_build_throws_exception_when_profile_id_not_set()
    {
        $builder = new ProfileContextBuilder();
        $builder->setRequest(new Request());
        $builder->setOwnEntityDescriptorProvider(new FixedEntityDescriptorProvider(new EntityDescriptor()));

        $builder->build();
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlBuildException
     * @expectedExceptionMessage Profile role not set
     */
    public function test_build_throws_exception_when_profile_role_not_set()
    {
        $builder = new ProfileContextBuilder();
        $builder->setRequest(new Request());
        $builder->setOwnEntityDescriptorProvider(new FixedEntityDescriptorProvider(new EntityDescriptor()));
        $builder->setProfileId(Profiles::METADATA);

        $builder->build();
    }
}
