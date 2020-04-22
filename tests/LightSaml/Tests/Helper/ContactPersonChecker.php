<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Helper;

use LightSaml\Model\Metadata\ContactPerson;
use LightSaml\Tests\BaseTestCase;

class ContactPersonChecker
{
    public static function check(
        BaseTestCase $test,
        $type,
        $company,
        $givenName,
        $surName,
        $email,
        $phone,
        ContactPerson $contact = null
    ) {
        $test->assertNotNull($contact);
        $test->assertEquals($type, $contact->getContactType());
        $test->assertEquals($company, $contact->getCompany());
        $test->assertEquals($givenName, $contact->getGivenName());
        $test->assertEquals($surName, $contact->getSurName());
        $test->assertEquals($email, $contact->getEmailAddress());
        $test->assertEquals($phone, $contact->getTelephoneNumber());
    }
}
