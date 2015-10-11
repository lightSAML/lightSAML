<?php

namespace LightSaml\Tests\Action;

use LightSaml\Action\DispatchEventAction;
use LightSaml\Context\ContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class DispatchEventActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger_event_dispatcher_and_event_name()
    {
        new DispatchEventAction(
            $this->getEventDispatcherMock(),
            'name'
        );
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
                $this->equalTo($expectedEventName),
                $this->isInstanceOf(GenericEvent::class)
            );

        $action->execute($context);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Context\ContextInterface
     */
    private function getContextMock()
    {
        return $this->getMock(ContextInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private function getEventDispatcherMock()
    {
        return $this->getMock(EventDispatcherInterface::class);
    }
}
