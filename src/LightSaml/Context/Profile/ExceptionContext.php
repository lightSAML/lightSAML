<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Context\Profile;

class ExceptionContext extends AbstractProfileContext
{
    /** @var \Exception */
    protected $exception;

    /** @var ExceptionContext|null */
    protected $nextExceptionContext;

    /**
     * @param \Exception|null $exception
     */
    public function __construct(\Exception $exception = null)
    {
        $this->exception = $exception;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return \Exception|null
     */
    public function getLastException()
    {
        if (null == $this->nextExceptionContext) {
            return $this->exception;
        }

        return $this->nextExceptionContext->getException();
    }

    /**
     * @return ExceptionContext|null
     */
    public function getNextExceptionContext()
    {
        return $this->nextExceptionContext;
    }

    /**
     * @param \Exception $exception
     *
     * @return ExceptionContext
     */
    public function addException(\Exception $exception)
    {
        if ($this->exception) {
            if (null == $this->nextExceptionContext) {
                $this->nextExceptionContext = new self($exception);

                return $this->nextExceptionContext;
            } else {
                return $this->nextExceptionContext->addException($exception);
            }
        } else {
            $this->exception = $exception;
        }

        return $this;
    }
}
