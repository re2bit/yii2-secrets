<?php

namespace yii\helpers;

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
        self::$stdOutBuffer .= $string;
        return true;
    }

    /**
     * @return string
     */
    public static function flushStdOutBuffer()
    {
        $result = self::$stdOutBuffer;
        $result = self::stripAnsiFormat($result);
        self::$stdOutBuffer = '';
        return $result;
    }
}
