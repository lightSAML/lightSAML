<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Context\Profile\Helper;

use LightSaml\Action\ActionInterface;
use LightSaml\Context\ContextInterface;
use LightSaml\Context\Profile\ProfileContext;

abstract class LogHelper
{
    /**
     * @param ContextInterface $context
     * @param ActionInterface  $action
     * @param array            $extraData
     *
     * @return array
     */
    public static function getActionContext(ContextInterface $context, ActionInterface $action, array $extraData = null)
    {
        return self::getContext($context, $action, $extraData, false);
    }

    /**
     * @param ContextInterface $context
     * @param ActionInterface  $action
     * @param array            $extraData
     *
     * @return array
     */
    public static function getActionErrorContext(ContextInterface $context, ActionInterface $action, array $extraData = null)
    {
        return self::getContext($context, $action, $extraData, true);
    }

    /**
     * @param ContextInterface $context
     * @param ActionInterface  $action
     * @param array            $extraData
     * @param bool             $logWholeContext
     *
     * @return array
     */
    private static function getContext(ContextInterface $context, ActionInterface $action = null, array $extraData = null, $logWholeContext = false)
    {
        $topContext = $context->getTopParent();
        $result = array();
        if ($topContext instanceof ProfileContext) {
            $result['profile_id'] = $topContext->getProfileId();
            $result['own_role'] = $topContext->getOwnRole();
        }
        if ($action) {
            $result['action'] = get_class($action);
        }
        $result['top_context_id'] = spl_object_hash($topContext);

        if ($logWholeContext) {
            $result['top_context'] = $topContext;
        }
        if ($extraData) {
            $result = array_merge($result, $extraData);
        }

        return $result;
    }
}
