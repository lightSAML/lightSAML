<?php

namespace LightSaml\Tests\Fixtures\Meta;

use LightSaml\Provider\TimeProvider\TimeProviderInterface;

class TimeProviderMock implements TimeProviderInterface
{
    /** @var  \DateTime */
    protected $value;

    /**
     * @param \DateTime $value
     */
    public function __construct(\DateTime $value = null)
    {
        $this->value = $value;
    }

    /**
     * @param \DateTime $value
     *
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
