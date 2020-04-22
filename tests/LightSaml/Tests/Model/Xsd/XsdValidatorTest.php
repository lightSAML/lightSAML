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

use LightSaml\Tests\BaseTestCase;
use LightSaml\Validator\Model\Xsd\XsdValidator;

class XsdValidatorTest extends BaseTestCase
{
    public function test_fails_on_invalid_xml()
    {
        $validator = new XsdValidator();
        $arr = $validator->validateProtocol('<a><');
        $this->assertGreaterThan(0, count($arr));
    }

    public function test_fails_on_empty_xml()
    {
        $validator = new XsdValidator();
        $arr = $validator->validateProtocol('<a><');
        $this->assertGreaterThan(0, count($arr));
    }
}
