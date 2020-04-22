<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Builder\Context;

use LightSaml\Builder\Context\ProfileContextBuilder;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Profile\Profiles;
use LightSaml\Provider\EntityDescriptor\FixedEntityDescriptorProvider;
use LightSaml\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Request;

class ProfileContextBuilderTest extends BaseTestCase
{
    public function test_constructs_without_arguments()
    {
        new ProfileContextBuilder();
        $this->assertTrue(true);
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

    public function test_build_throws_exception_when_request_not_set()
    {
        $this->expectException(\LightSaml\Error\LightSamlBuildException::class);
        $this->expectExceptionMessage('HTTP Request not set');

        $builder = new ProfileContextBuilder();

        $builder->build();
    }

    public function test_build_throws_exception_when_own_entity_descriptor_not_set()
    {
        $this->expectException(\LightSaml\Error\LightSamlBuildException::class);
        $this->expectExceptionMessage('Own EntityDescriptor not set');

        $builder = new ProfileContextBuilder();
        $builder->setRequest(new Request());

        $builder->build();
    }

    public function test_build_throws_exception_when_profile_id_not_set()
    {
        $this->expectException(\LightSaml\Error\LightSamlBuildException::class);
        $this->expectExceptionMessage('ProfileID not set');

        $builder = new ProfileContextBuilder();
        $builder->setRequest(new Request());
        $builder->setOwnEntityDescriptorProvider(new FixedEntityDescriptorProvider(new EntityDescriptor()));

        $builder->build();
    }

    public function test_build_throws_exception_when_profile_role_not_set()
    {
        $this->expectException(\LightSaml\Error\LightSamlBuildException::class);
        $this->expectExceptionMessage('Profile role not set');

        $builder = new ProfileContextBuilder();
        $builder->setRequest(new Request());
        $builder->setOwnEntityDescriptorProvider(new FixedEntityDescriptorProvider(new EntityDescriptor()));
        $builder->setProfileId(Profiles::METADATA);

        $builder->build();
    }
}
