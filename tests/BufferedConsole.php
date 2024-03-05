<?php

namespace yii\helpers;

use yii\helpers\BaseConsole;

class Console extends BaseConsole
{
    /**
     * @var string output buffer.
     */
    private static $stdOutBuffer = '';

    /**
     * @param $string
     * @return bool|int
     */
    public static function stdout($string)
    {
        static::$stdOutBuffer .= $string;
        return true;
    }

    /**
     * @return string
     */
    public static function flushStdOutBuffer()
    {
        $result = static::$stdOutBuffer;
        $result = self::stripAnsiFormat($result);
        static::$stdOutBuffer = '';
        return $result;
    }
}
