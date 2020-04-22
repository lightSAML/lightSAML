<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Action\Profile;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\ContextInterface;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Profile\Profiles;
use LightSaml\Tests\BaseTestCase;
use Psr\Log\LoggerInterface;

class AbstractProfileActionTest extends BaseTestCase
{
    public function test_calls_do_execute_with_profile_context()
    {
        /** @var LoggerInterface $loggerMock */
        $loggerMock = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $action = $this->getAbstractProfileActionMock($loggerMock);
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $action->expects($this->once())
            ->method('doExecute')
            ->with($profileContext);

        $action->execute($profileContext);
    }

    public function test_throws_exception_on_non_profile_context()
    {
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $this->expectExceptionMessage('Expected ProfileContext but got');

        /** @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject $loggerMock */
        $loggerMock = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $loggerMock->expects($this->once())
            ->method('emergency');

        $action = $this->getAbstractProfileActionMock($loggerMock);
        $context = $this->getContextMock();
        $action->expects($this->never())->method('doExecute');

        $action->execute($context);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Action\Profile\AbstractProfileAction
     */
    private function getAbstractProfileActionMock($loggerMock)
    {
        return $this->getMockForAbstractClass(AbstractProfileAction::class, [$loggerMock]);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Context\ContextInterface
     */
    private function getContextMock()
    {
        return $this->getMockBuilder(ContextInterface::class)->getMock();
    }
}
