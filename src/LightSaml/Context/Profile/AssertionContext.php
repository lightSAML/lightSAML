<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Context\Profile;

use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\EncryptedElement;

class AssertionContext extends AbstractProfileContext
{
    /** @var Assertion|null */
    private $assertion;

    /** @var EncryptedElement|null */
    private $encryptedAssertion;

    /** @var string */
    private $id;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return AssertionContext
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Assertion|null
     */
    public function getAssertion()
    {
        return $this->assertion;
    }

    /**
     * @param Assertion $assertion
     *
     * @return AssertionContext
     */
    public function setAssertion(Assertion $assertion = null)
    {
        $this->assertion = $assertion;

        return $this;
    }

    /**
     * @return EncryptedElement|null
     */
    public function getEncryptedAssertion()
    {
        return $this->encryptedAssertion;
    }

    /**
     * @param EncryptedElement $encryptedAssertion
     *
     * @return AssertionContext
     */
    public function setEncryptedAssertion(EncryptedElement $encryptedAssertion = null)
    {
        $this->encryptedAssertion = $encryptedAssertion;

        return $this;
    }
}
