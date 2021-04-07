<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action;

use LightSaml\Context\ContextInterface;

class CompositeAction implements ActionInterface, DebugPrintTreeActionInterface, CompositeActionInterface
{
    /** @var ActionInterface[] */
    protected $children = [];

    /**
     * @param ActionInterface[] $children
     */
    public function __construct(array $children = [])
    {
        foreach ($children as $action) {
            $this->add($action);
        }
    }

    /**
     * @return ActionInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return CompositeAction
     */
    public function add(ActionInterface $action)
    {
        $this->children[] = $action;

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return ActionInterface|null
     */
    public function map($callable)
    {
        foreach ($this->children as $k => $action) {
            $newAction = call_user_func($callable, $action);
            if ($newAction) {
                $this->children[$k] = $newAction;
            }
        }
    }

    /**
     * @return void
     */
    public function execute(ContextInterface $context)
    {
        foreach ($this->children as $action) {
            $action->execute($context);
        }
    }

    /**
     * @return array
     */
    public function debugPrintTree()
    {
        $arr = [];
        foreach ($this->children as $childAction) {
            if ($childAction instanceof DebugPrintTreeActionInterface) {
                $arr = array_merge($arr, $childAction->debugPrintTree());
            } else {
                $arr = array_merge($arr, [get_class($childAction) => []]);
            }
        }

        $result = [
            static::class => $arr,
        ];

        return $result;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->debugPrintTree(), JSON_PRETTY_PRINT);
    }
}
