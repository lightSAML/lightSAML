<?php

namespace LightSaml\Provider\TimeProvider;

interface TimeProviderInterface
{
    /**
     * @return int
     */
    public function getTimestamp();

    /**
     * @return \DateTime
     */
    public function getDateTime();
}
