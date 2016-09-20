<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Context;

abstract class AbstractContext implements ContextInterface
{
    /** @var ContextInterface|null */
    private $parent;

    /** @var ContextInterface[] */
    private $subContexts = array();

    /**
     * @return ContextInterface|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return ContextInterface
     */
    public function getTopParent()
    {
        if ($this->getParent()) {
            return $this->getParent()->getTopParent();
        }

        return $this;
    }

    /**
     * @param ContextInterface|null $parent
     *
     * @return ContextInterface
     */
    public function setParent(ContextInterface $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @param string      $name
     * @param null|string $class
     *
     * @return ContextInterface|null
     */
    public function getSubContext($name, $class = null)
    {
        if (isset($this->subContexts[$name])) {
            return $this->subContexts[$name];
        }

        if ($class) {
            $result = $this->createSubContext($class);
            $this->addSubContext($name, $result);

            return $result;
        }

        return null;
    }

    /**
     * @param string $class
     * @param bool   $autoCreate
     *
     * @return ContextInterface|null
     */
    public function getSubContextByClass($class, $autoCreate)
    {
        return $this->getSubContext($class, $autoCreate ? $class : null);
    }

    /**
     * @param string                  $name
     * @param object|ContextInterface $subContext
     *
     * @return AbstractContext
     */
    public function addSubContext($name, $subContext)
    {
        if (false === is_object($subContext)) {
            throw new \InvalidArgumentException('Expected object or ContextInterface');
        }

        $existing = isset($this->subContexts[$name]) ? $this->subContexts[$name] : null;
        if ($existing === $subContext) {
            return $this;
        }

        $this->subContexts[$name] = $subContext;
        if ($subContext instanceof ContextInterface) {
            $subContext->setParent($this);
        }

        if ($existing instanceof ContextInterface) {
            $existing->setParent(null);
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return ContextInterface
     */
    public function removeSubContext($name)
    {
        $subContext = $this->getSubContext($name, false);

        if ($subContext) {
            $subContext->setParent(null);
            unset($this->subContexts[$name]);
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function containsSubContext($name)
    {
        return isset($this->subContexts[$name]);
    }

    /**
     * @return ContextInterface
     */
    public function clearSubContexts()
    {
        foreach ($this->subContexts as $subContext) {
            $subContext->setParent(null);
        }
        $this->subContexts = array();

        return $this;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->subContexts);
    }

    /**
     * @param string $ownName
     *
     * @return array
     */
    public function debugPrintTree($ownName = 'root')
    {
        $result = array(
            $ownName => static::class,
        );

        if ($this->subContexts) {
            $arr = array();
            foreach ($this->subContexts as $name => $subContext) {
                if ($subContext instanceof ContextInterface) {
                    $arr = array_merge($arr, $subContext->debugPrintTree($name));
                } else {
                    $arr = array_merge($arr, array($name => get_class($subContext)));
                }
            }
            $result[$ownName.'__children'] = $arr;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->debugPrintTree(), JSON_PRETTY_PRINT);
    }

    /**
     * @param string $path
     *
     * @return ContextInterface
     */
    public function getPath($path)
    {
        if (is_string($path)) {
            $path = explode('/', $path);
        } elseif (false === is_array($path)) {
            throw new \InvalidArgumentException('Expected string or array');
        }

        $name = array_shift($path);
        $subContext = $this->getSubContext($name);
        if (null == $subContext) {
            return null;
        }

        if (empty($path)) {
            return $subContext;
        } else {
            return $subContext->getPath($path);
        }
    }

    /**
     * @param string $class
     *
     * @return ContextInterface
     */
    protected function createSubContext($class)
    {
        $result = new $class();

        return $result;
    }
}
