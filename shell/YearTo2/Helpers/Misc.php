<?php
namespace YearTo2\Helpers;

class Misc
{
    /**
     * 印出內容
     * @param  string $content 內容
     * @return
     */
    public static function disp($content)
    {
        echo print_r($content, true) . PHP_EOL;
    }
}