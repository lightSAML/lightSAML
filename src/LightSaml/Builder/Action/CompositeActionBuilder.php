<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Builder\Action;

use LightSaml\Action\ActionInterface;
use LightSaml\Action\CompositeAction;

class CompositeActionBuilder implements ActionBuilderInterface
{
    /**
     * int priority => ActionInterface[].
     *
     * @var array
     */
    private $actions = array();

    /** @var int */
    protected $increaseStep = 5;

    /** @var int */
    private $biggestPriority = 0;

    /**
     * @param ActionInterface $action
     * @param int|bool        $priority
     *
     * @return CompositeActionBuilder
     */
    public function add(ActionInterface $action, $priority = false)
    {
        if (false === $priority) {
            ++$this->biggestPriority;
            $priority = $this->biggestPriority;
        } elseif (false === is_int($priority)) {
            throw new \InvalidArgumentException('Expected integer value for priority');
        } elseif ($priority > $this->biggestPriority) {
            $this->biggestPriority = $priority;
        }

        if (false === isset($this->actions[$priority])) {
            $this->actions[$priority] = array();
        }
        $this->actions[$priority][] = $action;

        return $this;
    }

    /**
     * @return CompositeAction
     */
    public function build()
    {
        $actions = $this->actions;
        ksort($actions);

        $result = new CompositeAction();
        foreach ($actions as $arr) {
            foreach ($arr as $action) {
                $result->add($action);
            }
        }

        return $result;
    }
}
