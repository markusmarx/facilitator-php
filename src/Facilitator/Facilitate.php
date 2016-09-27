<?php
/**
 * 
 */

namespace Mxe\Facilitator\Facilitator;

use function \Functional\partial_right;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @class Facilitate
 *
 */
trait Facilitate
{

    /**
     * @var \Closure
     */
    private $replaceArgs;
    /**
     * @var \Closure
     */
    private $replaceRet;

    /**
     * @var PropertyAccess
     */
    private $propAccessor;

    public function invoke(\Closure $func, ...$arguments)
    {
        $arguments = $this->replaceArgs($arguments);
        $arguments = array_merge($arguments, [$this]);
        /** @var \Closure $func */
        $func = \Functional\partial_right($func, ...$arguments);
        $returns = $func(...$arguments);
        if (is_array($returns)) {
            $this->replaceRet($returns);
        }
        return $this;
    }


    private function replaceArgs($arguments)
    {
        if (!$this->replaceArgs) {
            $this->replaceArgs = function($e, $ctx, $access) {
                if (is_string($e) && $e[0] === '$') {
                    $v = $access->getValue($ctx, substr($e, 1));
                    return $v ? : $e;
                }
                return $e;
            };
            $this->replaceArgs = partial_right($this->replaceArgs, $this, $this->getPropertyAccessor());
        }
        return array_map($this->replaceArgs, $arguments);
    }


    private function replaceRet($returns)
    {
        if (!$this->replaceRet) {
            $this->replaceRet = function($v, $k, $col, $ctx, $access) {
                /** @var PropertyAccess $access */
                $access->setValue($ctx, $k, $v);
            };
            $this->replaceRet = partial_right($this->replaceRet, $this, $this->getPropertyAccessor());

        }
        \Functional\each($returns, $this->replaceRet);
    }


    private function getPropertyAccessor()
    {
        if (!$this->propAccessor) {
            $this->propAccessor = $access = PropertyAccess::createPropertyAccessor();
        }
        return $this->propAccessor;
    }

}

?>
