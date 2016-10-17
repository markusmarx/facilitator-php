<?php

namespace Facilitator\Functional\FunctionalCtxPrivate {

    use Symfony\Component\PropertyAccess\PropertyAccess;
    use \Functional as F;

    function getPropertyAccessor()
    {
        static $access;
        if (!$access) {
            $access = PropertyAccess::createPropertyAccessor();
        }
        return $access;
    }

    function replaceVariableFunc($ctx)
    {
        $func = function($e, $ctx, $access) {
            if (is_string($e) && $e[0] === '$') {
                $v = $access->getValue($ctx, substr($e, 1));
                return $v ? : $e;
            }
            return $e;
        };
        return F\partial_right($func, $ctx, getPropertyAccessor());
    }

    /**
     * @param callable $func
     * @param $returnMap
     * @return callable|\Closure
     */
    function mapReturnParameter(callable $func, $returnMap)
    {
        if (empty($returnMap)) {
            return $func;
        }
        return F\partial_left(replaceFunc(), $func, $returnMap);
    }

    function replaceFunc()
    {
        return function(callable $func, $returnMap, ...$arguments) {
            $ret = $func(...$arguments);
            if (is_array($ret)) {
                foreach ($returnMap as $source => $dest) {
                    if (isset($ret[$source])) {
                        $ret[$dest] = $ret[$source];
                        unset($ret[$source]);
                    }
                }
            }
            return $ret;
        };
    };

    function replaceArgs($ctx, $arguments)
    {
        return array_map(replaceVariableFunc($ctx), $arguments);
    }

    function replaceRet($ctx, $returns)
    {
        if (empty($returns)) {
            return;
        }

        $accessor = getPropertyAccessor();
        $accessor->setValue($ctx, 'lastResult', $returns);

        if (!is_array($returns)) {
            return;
        }

        $replaceRet = function($v, $k, $col, $ctx, $access) {
            /** @var PropertyAccess $access */
            $access->setValue($ctx, $k, $v);
        };
        $replaceRet = F\partial_right(
            $replaceRet,
            $ctx,
            $accessor
        );

        F\each($returns, $replaceRet);
    }


}