<?php
/**
 * Copyright (c) 2016.  Markus Marx
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NO LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
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
                if (is_string($e)) {
                    $v = $access->getValue($ctx, $e);
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
