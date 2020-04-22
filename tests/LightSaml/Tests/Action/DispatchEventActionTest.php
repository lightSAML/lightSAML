<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Action;

use LightSaml\Action\DispatchEventAction;
use LightSaml\Context\ContextInterface;
use LightSaml\Tests\BaseTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class DispatchEventActionTest extends BaseTestCase
{
    public function test_constructs_with_logger_event_dispatcher_and_event_name()
    {
        new DispatchEventAction(
            $this->getEventDispatcherMock(),
            'name'
        );
        $this->assertTrue(true);
    }

    public function test_dispatches_generic_event_on_execute()
    {
        $eventDispatcherMock = $this->getEventDispatcherMock();

        $action = new DispatchEventAction(
            $eventDispatcherMock,
            $expectedEventName = 'name'
        );

        $context = $this->getContextMock();

        $eventDispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->isInstanceOf(GenericEvent::class),
                $this->equalTo($expectedEventName)
            );

        $action->execute($context);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Context\ContextInterface
     */
    private function getContextMock()
    {
        return $this->getMockBuilder(ContextInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private function getEventDispatcherMock()
    {
        return $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
    }
}
