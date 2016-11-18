<?php

namespace LightSaml\Tests\Action\Profile;

use LightSaml\Action\Profile\FlushRequestStatesAction;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Context\Profile\ProfileContexts;
use LightSaml\Context\Profile\RequestStateContext;
use LightSaml\Profile\Profiles;
use LightSaml\State\Request\RequestState;
use LightSaml\Store\Request\RequestStateStoreInterface;
use Psr\Log\LoggerInterface;

class FlushRequestStatesActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger_and_request_state_store()
    {
        $loggerMock = $this->getLoggerMock();
        $requestStoreMock = $this->getRequestStateStoreMock();

        new FlushRequestStatesAction($loggerMock, $requestStoreMock);
    }

    public function test_flushes_store_with_inbound_request_state()
    {
        $loggerMock = $this->getLoggerMock();
        $requestStoreMock = $this->getRequestStateStoreMock();

        $action = new FlushRequestStatesAction($loggerMock, $requestStoreMock);

        $expectedIds = ['1111', '2222', '3333'];
        $context = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $context->getInboundContext()
            ->addSubContext(
                ProfileContexts::REQUEST_STATE,
                (new RequestStateContext())->setRequestState(new RequestState($expectedIds[0]))
            );
        $context->addSubContext(
            'assertion_1',
            (new AssertionContext())
                ->addSubContext(
                    ProfileContexts::REQUEST_STATE,
                    (new RequestStateContext())->setRequestState(new RequestState($expectedIds[1]))
                )
        );
        $context->addSubContext(
            'assertion_2',
            (new AssertionContext())
                ->addSubContext(
                    ProfileContexts::REQUEST_STATE,
                    (new RequestStateContext())->setRequestState(new RequestState($expectedIds[2]))
                )
        );

        $requestStoreMock->expects($this->exactly(3))
            ->method('remove')
            ->withConsecutive(
                [$this->equalTo($expectedIds[0])],
                [$this->equalTo($expectedIds[1])],
                [$this->equalTo($expectedIds[2])]
            )
            ->willReturnOnConsecutiveCalls(true, true, false)
        ;
        $loggerMock->expects($this->exactly(2))
            ->method('debug')
            ->withConsecutive(
                [$this->equalTo(sprintf('Removed request state "%s"', $expectedIds[0]))],
                [$this->equalTo(sprintf('Removed request state "%s"', $expectedIds[1]))],
                [$this->equalTo(sprintf('Request state "%s" does not exist', $expectedIds[2]))]
            );
        $loggerMock->expects($this->exactly(1))
            ->method('warning')
            ->withConsecutive(
                [$this->equalTo(sprintf('Request state "%s" does not exist', $expectedIds[2]))]
            );

        $action->execute($context);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Store\Request\RequestStateStoreInterface
     */
    private function getRequestStateStoreMock()
    {
        return $this->getMock(RequestStateStoreInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface
     */
    private function getLoggerMock()
    {
        return $this->getMock(LoggerInterface::class);
    }
}
