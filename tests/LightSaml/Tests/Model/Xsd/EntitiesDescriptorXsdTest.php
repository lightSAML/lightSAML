<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Model\Xsd;

use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Model\Metadata\EntitiesDescriptor;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\SamlConstants;

class EntitiesDescriptorXsdTest extends AbstractXsdValidationTest
{
    public function test_entities_descriptor_with_xsd()
    {
        $entitiesDescriptor = new EntitiesDescriptor();
        $entitiesDescriptor->addItem($ed1 = new EntityDescriptor('https://ed1.com'));
        $entitiesDescriptor->addItem($es1 = new EntitiesDescriptor());
        $es1->addItem($ed2 = new EntityDescriptor('https://ed2.com'));
        $entitiesDescriptor->addItem($ed3 = new EntityDescriptor('https://ed3.com'));

        $this->fillEntityDescriptor($ed1);
        $this->fillEntityDescriptor($ed2);
        $this->fillEntityDescriptor($ed3);

        $this->sign($entitiesDescriptor);

        $this->validateMetadata($entitiesDescriptor);
    }

    private function fillEntityDescriptor(EntityDescriptor $ed)
    {
        $ed->addItem($sp = new SpSsoDescriptor());
        $sp->addAssertionConsumerService(new AssertionConsumerService('https://location.com', SamlConstants::BINDING_SAML2_HTTP_POST));
    }
}
