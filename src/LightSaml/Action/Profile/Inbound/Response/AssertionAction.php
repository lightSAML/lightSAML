<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Inbound\Response;

use LightSaml\Action\ActionInterface;
use LightSaml\Action\DebugPrintTreeActionInterface;
use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use Psr\Log\LoggerInterface;

class AssertionAction extends AbstractProfileAction implements DebugPrintTreeActionInterface
{
    /** @var ActionInterface */
    private $assertionAction;

    /**
     * @param LoggerInterface $logger
     * @param ActionInterface $assertionAction
     */
    public function __construct(LoggerInterface $logger, ActionInterface $assertionAction)
    {
        parent::__construct($logger);

        $this->assertionAction = $assertionAction;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $response = MessageContextHelper::asResponse($context->getInboundContext());

        foreach ($response->getAllAssertions() as $index => $assertion) {
            $name = sprintf('assertion_%s', $index);
            /** @var AssertionContext $assertionContext */
            $assertionContext = $context->getSubContext($name, AssertionContext::class);
            $assertionContext
                ->setAssertion($assertion)
                ->setId($name)
            ;

            $this->assertionAction->execute($assertionContext);
        }
    }

    /**
     * @param int $depth
     *
     * @return array
     */
    public function debugPrintTree($depth = 0)
    {
        $arr = array();
        if ($this->assertionAction instanceof DebugPrintTreeActionInterface) {
            $arr = array_merge($arr, $this->assertionAction->debugPrintTree());
        } else {
            $arr[get_class($this->assertionAction)] = array();
        }

        $result = array(
            static::class => $arr,
        );

        return $result;
    }
}
