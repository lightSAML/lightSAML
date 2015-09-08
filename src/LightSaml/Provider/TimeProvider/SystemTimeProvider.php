<?php

namespace LightSaml\Provider\TimeProvider;

class SystemTimeProvider implements TimeProviderInterface
{
    /**
     * @return int
     */
    public function getTimestamp()
    {
        return time();
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return new \DateTime();
    }
}
