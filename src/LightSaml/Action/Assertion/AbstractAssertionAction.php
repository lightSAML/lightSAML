<?php

namespace LightSaml\Action\Assertion;

use LightSaml\Action\ActionInterface;
use LightSaml\Context\AbstractContext;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Error\LightSamlContextException;
use Psr\Log\LoggerInterface;

abstract class AbstractAssertionAction implements ActionInterface
{
    /** @var  LoggerInterface */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param AbstractContext $context
     *
     * @return void
     */
    public function execute(AbstractContext $context)
    {
        if ($context instanceof AssertionContext) {
            $this->doExecute($context);
        } else {
            throw new LightSamlContextException($context, 'Expected AssertionContext');
        }
    }

    /**
     * @param AssertionContext $context
     *
     * @return void
     */
    abstract protected function doExecute(AssertionContext $context);
}
