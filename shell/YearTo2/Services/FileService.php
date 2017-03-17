<?php

namespace Services;

class FileService
{
    /**
     * 列出目錄內容(排除 . 與 ..)
     * @param  string $path 目錄路徑
     * @return array
     */
    public function scanDir($path, $ignores = ['.', '..'])
    {
        $contents = [];
        if (is_dir($path) === true) {
            $list = scandir($path);
            foreach ($list as $content) {
                if (in_array($content, $ignores) === false) {
                    array_push($contents, $content);
                }
            }
        }
        return $contents;
    }
}