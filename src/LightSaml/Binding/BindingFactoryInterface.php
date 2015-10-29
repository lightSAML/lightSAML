<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Binding;

use Symfony\Component\HttpFoundation\Request;

interface BindingFactoryInterface
{
    /**
     * @param Request $request
     *
     * @return AbstractBinding
     */
    public function getBindingByRequest(Request $request);

    /**
     * @param string $bindingType
     *
     * @throws \LightSaml\Error\LightSamlBindingException
     *
     * @return AbstractBinding
     */
    public function create($bindingType);

    /**
     * @param Request $request
     *
     * @return string|null
     */
    public function detectBindingType(Request $request);
}
