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


namespace Mxe\Facilitator\Lang;

/**
 * @class Enum is an basic implementation of enum. This is copied from
 * http://php.net/manual/de/class.splenum.php#117247.
 * @package Mxe\Facilitator\Lang
 * @version 0.1
 */
abstract class Enum {

    /**
     * @var array
     */
    private static $constCacheArray = NULL;

    /**
     * Enum constructor. Invisible.
     */
    private function __construct(){
    }

    /**
     * Returns array with enum values.
     *
     * @return array
     */
    private static function getConstants() {
        if (self::$constCacheArray == NULL) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }

    /**
     * Returns true if enum value is valid otherwise false. If strict is true then the name is compared case sensitive.
     *
     * @param string $name   name of the enum value
     * @param bool   $strict if strict is true then the name is compared case sensitive.
     *
     * @return bool true if $name is a valid enum name otherwise false
     */
    public static function isValidName($name, $strict = false) {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    /**
     * Returns true if given $value is a valid enum value otherwise false.
     *
     * @param mixed $value enum value to test
     *
     * @return bool true if enum value exist otherwise false
     */
    public static function isValidValue($value) {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict = true);
    }
}

?>
