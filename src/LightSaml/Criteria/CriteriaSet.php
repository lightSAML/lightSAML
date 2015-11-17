<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Criteria;

class CriteriaSet
{
    /** @var array|CriteriaInterface[] */
    protected $criterions = array();

    /**
     * @param CriteriaInterface[] $criterions
     */
    public function __construct(array $criterions = array())
    {
        foreach ($criterions as $criterion) {
            $this->add($criterion);
        }
    }

    /**
     * @param CriteriaInterface $criteria
     *
     * @return CriteriaSet
     */
    public function add(CriteriaInterface $criteria)
    {
        $this->criterions[] = $criteria;

        return $this;
    }

    /**
     * @param CriteriaInterface $criteria
     *
     * @return CriteriaSet
     */
    public function addIfNone(CriteriaInterface $criteria)
    {
        if (false == $this->has(get_class($criteria))) {
            $this->add($criteria);
        }

        return $this;
    }

    /**
     * @param CriteriaSet $criteriaSet
     *
     * @return CriteriaSet
     */
    public function addAll(CriteriaSet $criteriaSet)
    {
        foreach ($criteriaSet->all() as $criteria) {
            $this->add($criteria);
        }

        return $this;
    }

    /**
     * @param mixed    $condition
     * @param callable $callback
     *
     * @return CriteriaSet
     */
    public function addIf($condition, $callback)
    {
        if ($condition) {
            $criteria = call_user_func($callback);
            if ($criteria) {
                $this->add($criteria);
            }
        }

        return $this;
    }

    /**
     * @return CriteriaInterface[]|array
     */
    public function all()
    {
        return $this->criterions;
    }

    /**
     * @param string $class
     *
     * @return array|CriteriaInterface[]
     */
    public function get($class)
    {
        $result = array();
        foreach ($this->criterions as $criteria) {
            if ($criteria instanceof $class) {
                $result[] = $criteria;
            }
        }

        return $result;
    }

    /**
     * @param string $class
     *
     * @return CriteriaInterface|null
     */
    public function getSingle($class)
    {
        foreach ($this->criterions as $criteria) {
            if ($criteria instanceof $class) {
                return $criteria;
            }
        }

        return null;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function has($class)
    {
        foreach ($this->criterions as $criteria) {
            if ($criteria instanceof $class) {
                return true;
            }
        }

        return false;
    }
}
