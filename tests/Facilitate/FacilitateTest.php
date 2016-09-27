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


namespace Mxe\Facilitator\Factilitator\Tests;
use Mxe\Facilitator\Facilitator\Facilitate;

/**
 * @class StreamTest
 *
 */
class FacilitateTest extends \PHPUnit_Framework_TestCase
{

    protected $variable = 'test';

    use Facilitate;

    /**
     * @test
     */
    public function testInvokeWithoutParams()
    {
        $testFunc = function (FacilitateTest $ctx) {
            $ctx->assertEquals('test', $ctx->getVariable());
            $ctx->assertInstanceOf(FacilitateTest::class, $ctx);
        };
        $this->invoke($testFunc);
    }

    /**
     * @test
     */
    public function testInvokeWithParams()
    {
        $testFunc = function ($i, FacilitateTest $ctx) {
            $ctx->assertEquals(1, $i);
            $ctx->assertEquals('test', $ctx->getVariable());
            $ctx->assertInstanceOf(FacilitateTest::class, $ctx);
        };
        $this
            ->invoke($testFunc, 1)
            ;
    }

    /**
     * @test
     */
    public function testInvokeWithCtxParams()
    {
        $testFunc = function ($i, $str, FacilitateTest $ctx) {
            $ctx->assertEquals(1, $i);
            $ctx->assertEquals('test', $str);
            $ctx->assertInstanceOf(FacilitateTest::class, $ctx);
        };
        $this->invoke($testFunc, 1, 'variable');
    }

    /**
     * @test
     */
    public function testInvokeWithReturnParams()
    {
        $testFunc1 = function ($i, $str, FacilitateTest $ctx) {
            $ctx->assertEquals(1, $i);
            $ctx->assertEquals('test', $str);
            $ctx->assertInstanceOf(FacilitateTest::class, $ctx);
            return ['variable'=>'test1'];
        };
        $testFunc2 = function ($i, $str, FacilitateTest $ctx) {
            $ctx->assertEquals(2, $i);
            $ctx->assertEquals('test1', $str);
            $ctx->assertInstanceOf(FacilitateTest::class, $ctx);
        };
        $this
            ->invoke($testFunc1, 1, 'variable')
            ->invoke($testFunc2, 2, 'variable');
    }

    public function getVariable()
    {
        return $this->variable;
    }

    public function setVariable($v)
    {
        $this->variable = $v;
    }

}

?>
