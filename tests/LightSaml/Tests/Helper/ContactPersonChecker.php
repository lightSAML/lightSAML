<?php

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
