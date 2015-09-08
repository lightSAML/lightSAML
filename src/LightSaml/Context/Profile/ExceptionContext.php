<?php

namespace LightSaml\Context\Profile;

class ExceptionContext extends AbstractProfileContext
{
    /** @var \Exception */
    protected $exception;

    /** @var  ExceptionContext|null */
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
                $this->nextExceptionContext = new ExceptionContext($exception);

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
