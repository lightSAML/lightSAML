<?php

namespace LightSaml\Context\Profile\Helper;

use LightSaml\Action\ActionInterface;
use LightSaml\Context\AbstractContext;
use LightSaml\Context\Profile\ProfileContext;

abstract class LogHelper
{
    /**
     * @param AbstractContext $context
     * @param ActionInterface $action
     * @param array           $extraData
     *
     * @return array
     */
    public static function getActionContext(AbstractContext $context, ActionInterface $action, array $extraData = null)
    {
        return self::getContext($context, $action, $extraData, false);
    }

    /**
     * @param AbstractContext $context
     * @param ActionInterface $action
     * @param array           $extraData
     *
     * @return array
     */
    public static function getActionErrorContext(AbstractContext $context, ActionInterface $action, array $extraData = null)
    {
        return self::getContext($context, $action, $extraData, true);
    }

    /**
     * @param AbstractContext $context
     * @param ActionInterface $action
     * @param array           $extraData
     * @param bool            $logWholeContext
     *
     * @return array
     */
    private static function getContext(AbstractContext $context, ActionInterface $action = null, array $extraData = null, $logWholeContext = false)
    {
        $topContext =  $context->getTopParent();
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
