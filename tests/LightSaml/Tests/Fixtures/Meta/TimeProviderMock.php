<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Fixtures\Meta;

use LightSaml\Provider\TimeProvider\TimeProviderInterface;

class TimeProviderMock implements TimeProviderInterface
{
    /** @var \DateTime */
    protected $value;

    /**
     * @param \DateTime $value
     */
    public function __construct(\DateTime $value = null)
    {
        $this->value = $value;
    }

    /**
     * @return TimeProviderMock
     */
    public function setNow(\DateTime $value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->value->getTimestamp();
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->value;
    }
}
