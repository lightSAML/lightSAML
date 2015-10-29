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

interface ContextInterface extends \IteratorAggregate
{
    /**
     * @return ContextInterface|null
     */
    public function getParent();

    /**
     * @return ContextInterface
     */
    public function getTopParent();

    /**
     * @param ContextInterface|null $parent
     *
     * @return ContextInterface
     */
    public function setParent(ContextInterface $parent = null);

    /**
     * @param string      $name
     * @param null|string $class
     *
     * @return ContextInterface|null
     */
    public function getSubContext($name, $class = null);

    /**
     * @param string $class
     * @param bool   $autoCreate
     *
     * @return ContextInterface|null
     */
    public function getSubContextByClass($class, $autoCreate);

    /**
     * @param string                  $name
     * @param object|ContextInterface $subContext
     */
    public function addSubContext($name, $subContext);

    /**
     * @param string $name
     *
     * @return ContextInterface
     */
    public function removeSubContext($name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function containsSubContext($name);

    /**
     * @return ContextInterface
     */
    public function clearSubContexts();

    /**
     * @param string $ownName
     *
     * @return array
     */
    public function debugPrintTree($ownName = 'root');

    /**
     * @param string $path
     *
     * @return ContextInterface
     */
    public function getPath($path);
}
