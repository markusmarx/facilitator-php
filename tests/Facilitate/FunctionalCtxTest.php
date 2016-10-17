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


namespace Facilitator\Functional\Tests;

use Facilitator\Functional\FunctionalCtx;

/**
 * @class StreamTest
 *
 */
class FunctionalCtxTest extends \PHPUnit_Framework_TestCase
{

    protected $variable = 'test';

    use FunctionalCtx;

    /**
     * @test
     */
    public function testInvokeWithoutParams()
    {
        $this->variable = 'test';
        $testFunc = function (FunctionalCtxTest $ctx) {
            $ctx->assertEquals('test', $ctx->getVariable());
            $ctx->assertInstanceOf(FunctionalCtxTest::class, $ctx);
        };
        $this->invoke($testFunc, $this);

    }

    /**
     * @test
     */
    public function testGetLastResult()
    {
        $testFunc = function () {
            return 'lastResult';
        };
        $this->invoke($testFunc);
        $this->assertEquals('lastResult', $this->getLastResult());

    }

    /**
     * @test
     */
    public function testInvokeWithParams()
    {
        $this->variable = 'test';
        $testFunc = function ($i, FunctionalCtxTest $ctx) {
            $ctx->assertEquals(1, $i);
            $ctx->assertEquals('test', $ctx->getVariable());
            $ctx->assertInstanceOf(FunctionalCtxTest::class, $ctx);
        };
        $this
            ->invoke($testFunc, 1, $this)
            ;
    }

    /**
     * @test
     */
    public function testInvokeWithCtxParams()
    {
        $this->variable = 'test';
        $testFunc = function ($i, $str, FunctionalCtxTest $ctx) {
            $ctx->assertEquals(1, $i);
            $ctx->assertEquals('test', $str);
            $ctx->assertInstanceOf(FunctionalCtxTest::class, $ctx);
        };
        $this->invoke($testFunc, 1, '$variable', $this);
    }

    /**
     * @test
     */
    public function testInvokeWithReturnParams()
    {
        $this->setVariable('test');
        $testFunc1 = function ($i, $str, FunctionalCtxTest $ctx) {
            $ctx->assertEquals(1, $i);
            $ctx->assertEquals('test', $str);
            $ctx->assertInstanceOf(FunctionalCtxTest::class, $ctx);
            return ['variable'=>'test1'];
        };
        $testFunc2 = function ($i, $str, FunctionalCtxTest $ctx) {
            $ctx->assertEquals(2, $i);
            $ctx->assertEquals('test1', $str);
            $ctx->assertInstanceOf(FunctionalCtxTest::class, $ctx);
        };
        $this
            ->invoke($testFunc1, 1, '$variable', $this)
            ->invoke($testFunc2, 2, '$variable', $this);
    }

    public function testCreateFunction()
    {
        $this->setVariable('');
        $testFunc1 = function ($i, FunctionalCtxTest $ctx, $str) {
            $ctx->assertEquals(1, $i);
            $ctx->assertEquals('test', $str);
            $ctx->assertInstanceOf(FunctionalCtxTest::class, $ctx);
            return ['variable1'=>'test1'];
        };

        $testFunc1 = self::createFunction(
            $testFunc1,
            ['test'],
            ['variable1' => 'variable']
        );
        $this
            ->invoke($testFunc1, 1, $this);
        $this->assertEquals('test1', $this->getVariable());

    }

    /**
     * @expectedException \Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException
     */
    public function testCreateFunctionWithInvalidMap()
    {
        $this->setVariable('');
        $testFunc1 = function ($i, FunctionalCtxTest $ctx, $str) {
            $ctx->assertEquals(1, $i);
            $ctx->assertEquals('test', $str);
            $ctx->assertInstanceOf(FunctionalCtxTest::class, $ctx);
            return ['variable1'=>'test1'];
        };

        $testFunc1 = self::createFunction(
            $testFunc1,
            ['test'],
            null
        );
        $this
            ->invoke($testFunc1, 1, $this);
        $this->assertEquals('', $this->getVariable());

    }

    public function testInvokeWithFunctionName()
    {
        $this->setVariable('');
        $this->invoke('Facilitator\Functional\Tests\testFunction', $this);
        $this->assertEquals('run', $this->getVariable());
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

function testFunction(FunctionalCtxTest $obj)
{
    $obj->setVariable('run');
}

?>
