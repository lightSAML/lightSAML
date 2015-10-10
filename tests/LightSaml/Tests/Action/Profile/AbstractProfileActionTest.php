<?php

namespace LightSaml\Tests\Action\Profile;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\ContextInterface;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Profile\Profiles;
use Psr\Log\LoggerInterface;

class AbstractProfileActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_calls_do_execute_with_profile_context()
    {
        /** @var LoggerInterface $loggerMock */
        $loggerMock = $this->getMock(LoggerInterface::class);
        $action = $this->getAbstractProfileActionMock($loggerMock);
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $action->expects($this->once())
            ->method('doExecute')
            ->with($profileContext);

        $action->execute($profileContext);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Expected ProfileContext but got
     */
    public function test_throws_exception_on_non_profile_context()
    {
        /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $loggerMock */
        $loggerMock = $this->getMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('emergency');

        $action = $this->getAbstractProfileActionMock($loggerMock);
        $context = $this->getContextMock();
        $action->expects($this->never())->method('doExecute');

        $action->execute($context);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Action\Profile\AbstractProfileAction
     */
    private function getAbstractProfileActionMock($loggerMock)
    {
        return $this->getMockForAbstractClass(AbstractProfileAction::class, [$loggerMock]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Context\ContextInterface
     */
    private function getContextMock()
    {
        return $this->getMock(ContextInterface::class);
    }
}
