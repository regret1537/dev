<?php
namespace YearTo2\Helpers;

class Misc
{
    /**
     * �L�X���e
     * @param  string $content ���e
     * @return
     */
    public static function disp($content)
    {
        echo print_r($content, true) . PHP_EOL;
    }
}