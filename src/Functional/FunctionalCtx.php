<?php
/**
 *
 */

namespace Facilitator\Functional;

use \Functional as F;

require __DIR__ . '/FunctionalCtxPrivate.php';

/**
 * @class Facilitate
 *
 */
trait FunctionalCtx
{

    protected $lastResult;

    final public function invoke(callable $func, ...$arguments)
    {
        $arguments = FunctionalCtxPrivate\replaceArgs($this, $arguments);
        $arguments[] = $this;
        $returns = $func(...$arguments);
        FunctionalCtxPrivate\replaceRet($this, $returns);
        return $this;
    }

    static public function createFunction(callable $func, $fixedArgs = null, $returnMap = null)
    {
        if (!empty($fixedArgs)) {
            $func = F\partial_right($func, ...$fixedArgs);
        }

        return FunctionalCtxPrivate\mapReturnParameter(
            $func,
            $returnMap
        );

    }

    /**
     * @return mixed
     */
    public function getLastResult()
    {
        return $this->lastResult;
    }

    /**
     * @param mixed $lastResult
     */
    public function setLastResult($lastResult)
    {
        $this->lastResult = $lastResult;
    }

}

?>
