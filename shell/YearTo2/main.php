<?php
// �ޤJ���n�ɮ�
$includeList = ['Helpers/Misc.php', 'Services/FormatService.php', 'Services/FileService.php'];
foreach ($includeList as $path) {
    include($path);
}

use YearTo2\Helpers\Misc;
use Services\FormatService;
use Services\FileService;

$fileService = new FileService();
$formatService = new FormatService();

$rootPath = dirname(__FILE__) . '/Files'; // �ڥؿ�

// ���o�ؿ����e
$list = $fileService->scanDir($rootPath);

foreach ($list as $dirName) {
    // �ˬd�O�_���ؿ�
    $currentPath = $rootPath . '/' . $dirName;
    if (is_dir($currentPath) === true) {
        // �ˬd�ؿ��W�٫e 4 �X�O�_�� 4 �X�褸�~
        if ($formatService->is4BitsYearDir($dirName) === true) {
            // 4 �X�� 2 �X
            $newName = $formatService->to2BitsYear($dirName);
            $newPath = $rootPath. '/' . $newName;
            $result = $formatService->rename($currentPath, $newPath);
            if ($result === []) {
                Misc::disp($dirName . ' to ' . $newName);
            }
        }
    }
}