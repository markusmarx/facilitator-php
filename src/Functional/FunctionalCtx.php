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

    /**
     * Invoke a function and set the result in context instance. Arguments started with $ are replaced by
     * PropertyAccessor.
     *
     * @param callable $func      callable function
     * @param array    $arguments arguments to call function
     *
     * @return $this
     */
    final public function invoke(callable $func, ...$arguments)
    {
        $arguments = FunctionalCtxPrivate\replaceArgs($this, $arguments);
        return $this->mapResult($func(...$arguments));
    }

    /**
     *
     * @param array $result
     *
     * @return $this
     */
    final public function mapResult($result) {
        FunctionalCtxPrivate\replaceRet($this, $result);
        return $this;
    }


    /**
     * Create a function from the given callable. The $fixedArgs is used to create a function with default args by
     * partial_right. The $returnMap is used to map the returned values in the context object.
     *
     * @param callable   $func      callable to wrap
     * @param array|null $fixedArgs fixed arguments
     * @param array|null $returnMap
     *
     * @return callable|\Closure
     */
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
