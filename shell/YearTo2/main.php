<?php
// 引入必要檔案
$includeList = ['Helpers/Misc.php', 'Services/FormatService.php', 'Services/FileService.php'];
foreach ($includeList as $path) {
    include($path);
}

use YearTo2\Helpers\Misc;
use Services\FormatService;
use Services\FileService;

$fileService = new FileService();
$formatService = new FormatService();

$rootPath = dirname(__FILE__) . '/Files'; // 根目錄

// 取得目錄內容
$list = $fileService->scanDir($rootPath);

foreach ($list as $dirName) {
    // 檢查是否為目錄
    $currentPath = $rootPath . '/' . $dirName;
    if (is_dir($currentPath) === true) {
        // 檢查目錄名稱前 4 碼是否為 4 碼西元年
        if ($formatService->is4BitsYearDir($dirName) === true) {
            // 4 碼轉 2 碼
            $newName = $formatService->to2BitsYear($dirName);
            $newPath = $rootPath. '/' . $newName;
            $result = $formatService->rename($currentPath, $newPath);
            if ($result === []) {
                Misc::disp($dirName . ' to ' . $newName);
            }
        }
    }
}