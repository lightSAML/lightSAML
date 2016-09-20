<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action;

use LightSaml\Context\ContextInterface;
use LightSaml\Context\Profile\ExceptionContext;
use LightSaml\Context\Profile\ProfileContexts;

class CatchableErrorAction implements ActionInterface
{
    /** @var ActionInterface */
    protected $mainAction;

    /** @var ActionInterface */
    protected $errorAction;

    /**
     * @param ActionInterface $mainAction
     * @param ActionInterface $errorAction
     */
    public function __construct(ActionInterface $mainAction, ActionInterface $errorAction)
    {
        $this->mainAction = $mainAction;
        $this->errorAction = $errorAction;
    }

    /**
     * @param ContextInterface $context
     *
     * @return void
     */
    public function execute(ContextInterface $context)
    {
        try {
            $this->mainAction->execute($context);
        } catch (\Exception $ex) {
            /** @var ExceptionContext $exceptionContext */
            $exceptionContext = $context->getSubContext(ProfileContexts::EXCEPTION, ExceptionContext::class);
            $exceptionContext->addException($ex);

            $this->errorAction->execute($context);
        }
    }
}
