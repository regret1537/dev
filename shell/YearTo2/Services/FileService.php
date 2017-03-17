<?php

namespace Services;

class FileService
{
    /**
     * �C�X�ؿ����e(�ư� . �P ..)
     * @param  string $path �ؿ����|
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