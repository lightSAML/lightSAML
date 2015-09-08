<?php

namespace LightSaml\Builder\Profile;

interface ProfileBuilderInterface
{
    /**
     * @return \LightSaml\Action\CompositeAction
     */
    public function buildAction();

    /**
     * @return \LightSaml\Context\Profile\ProfileContext
     */
    public function buildContext();
}
