<?php

namespace LightSaml\Resolver\Endpoint\Criteria;

use LightSaml\Criteria\CriteriaInterface;

class BindingCriteria implements CriteriaInterface
{
    /**
     * Binding => Preference
     *
     * @var  int[]
     */
    protected $bindings;

    /**
     * @param string[] $bindings Ordered by preference, first being most preferable, last least preferable
     */
    public function __construct(array $bindings)
    {
        foreach ($bindings as $binding) {
            $this->add($binding);
        }
    }

    /**
     * @param string $binding Next preferable binding
     *
     * @return BindingCriteria
     */
    public function add($binding)
    {
        $this->bindings[$binding] = count($this->bindings) + 1;

        return $this;
    }

    /**
     * Returns array of bindings ordered by preference, first being most preferable, last least preferable
     *
     * @return string[]
     */
    public function getAllBindings()
    {
        return array_keys($this->bindings);
    }

    /**
     * @param $binding
     *
     * @return int|null Preference of a binding or null if not preferred
     */
    public function getPreference($binding)
    {
        return @$this->bindings[$binding];
    }
}
