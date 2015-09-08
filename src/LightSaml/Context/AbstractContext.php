<?php

namespace LightSaml\Context;

abstract class AbstractContext implements \IteratorAggregate
{
    /** @var  AbstractContext|null */
    private $parent;

    /** @var AbstractContext[] */
    private $subContexts = array();

    /**
     * @return AbstractContext|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return AbstractContext
     */
    public function getTopParent()
    {
        if ($this->getParent()) {
            return $this->getParent()->getTopParent();
        }

        return $this;
    }

    /**
     * @param AbstractContext|null $parent
     *
     * @return AbstractContext
     */
    public function setParent(AbstractContext $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @param string      $name
     * @param null|string $class
     *
     * @return AbstractContext|null
     */
    public function getSubContext($name, $class = null)
    {
        $result = @$this->subContexts[$name];
        if ($result) {
            return $result;
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
     * @return AbstractContext|null
     */
    public function getSubContextByClass($class, $autoCreate)
    {
        return $this->getSubContext($class, $autoCreate ? $class : null);
    }

    /**
     * @param string                 $name
     * @param object|AbstractContext $subContext
     */
    public function addSubContext($name, $subContext)
    {
        if (false === is_object($subContext)) {
            throw new \InvalidArgumentException('Expected object or AbstractContext');
        }

        $existing = @$this->subContexts[$name];
        if ($existing === $subContext) {
            return;
        }

        $this->subContexts[$name] = $subContext;
        if ($subContext instanceof AbstractContext) {
            $subContext->setParent($this);
        }

        if ($existing instanceof AbstractContext) {
            $existing->setParent(null);
        }
    }

    /**
     * @param string $name
     *
     * @return AbstractContext
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
     * @return AbstractContext
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
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     *                      <b>Traversable</b>
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
                if ($subContext instanceof AbstractContext) {
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
     * @return AbstractContext
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
     * @return AbstractContext
     */
    protected function createSubContext($class)
    {
        $result = new $class();

        return $result;
    }
}
