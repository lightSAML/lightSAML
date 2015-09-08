<?php

namespace LightSaml\Credential\Context;

class CredentialContextSet
{
    /** @var CredentialContextInterface[] */
    protected $contexts = array();

    /**
     * @param CredentialContextInterface[] $contexts
     */
    public function __construct(array $contexts = array())
    {
        foreach ($contexts as $context) {
            if (false == $context instanceof CredentialContextInterface) {
                throw new \InvalidArgumentException('Expected CredentialContextInterface');
            }
            $this->contexts[] = $context;
        }
    }

    /**
     * @return CredentialContextInterface[]
     */
    public function all()
    {
        return $this->contexts;
    }

    /**
     * @param string $class
     *
     * @return CredentialContextInterface|null
     */
    public function get($class)
    {
        foreach ($this->contexts as $context) {
            if (get_class($context) == $class || is_subclass_of($context, $class)) {
                return $context;
            }
        }

        return null;
    }
}
