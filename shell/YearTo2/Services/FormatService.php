<?php

namespace Services;

class FormatService
{
    /**
     * 是否為 4碼 西元年開頭的目錄
     * @param  string $dirName 目錄名稱
     * @return boolean
     */
    public function is4BitsYearDir($dirName)
    {
        $dateString = $this->left($dirName, 8);
        $date = date('Ymd', strtotime($dateString));
        return ($dateString === $date);
    }

    /**
     * 左邊取 N 位
     * @param  string  $content 內容
     * @param  integer $length
     * @return string
     */
    public function left($content, $length)
    {
        return substr($content, 0, $length);   
    }

    /**
     * 更名(目錄中有檔案也可使用)
     * @param  string $old 舊路徑
     * @param  string $new 新路徑
     * @return array
     */
    public function rename($old, $new)
    {
        exec('mv "' . $old . '" "' . $new . '"', $output);
        return $output;
    }

    /**
     * 4 碼西元年轉 2 碼
     * @param  string $dirName 目錄名稱
     * @return string        
     */
    public function to2BitsYear($dirName)
    {
        $year4 = $this->left($dirName, 4);
        $year2 = substr($dirName, 2, 2);
        return str_replace($year4, $year2, $dirName);
    }
}