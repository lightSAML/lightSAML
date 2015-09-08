<?php

namespace LightSaml\Tests\Helper;

use LightSaml\Model\Metadata\ContactPerson;

class ContactPersonChecker
{
    public static function check(
        \PHPUnit_Framework_TestCase $test,
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
