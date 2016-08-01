<?php
    include('../tpl/header.php');
    include('../lib/html_common.inc');
    include('../lib/misc.inc');
    include('../lib/cls_file.php');
    
    $cMisc = new misc();
    $cFile = new cls_file();
    
    function get_search_file($aFile_List) {
        $aSearch_Files = array();
        foreach ($aFile_List as $aTmp_Path) {
            if (is_array($aTmp_Path)) {
                $aTmp_List = get_search_file($aTmp_Path);
                foreach ($aTmp_List as $sTmp_Path) {
                    $aSearch_Files[] = $sTmp_Path;
                }
            } else {
                $aSearch_Files[] = $aTmp_Path;
            }
        }
        return $aSearch_Files;
    }
    
    function get_name_count($sFile_Name, $aSearch_File_List) {
        $aName_Count = array();
        foreach ($aSearch_File_List as $sTmp_Path) {
            $sTmp_Content = file_get_contents($sTmp_Path);
            $iTmp_Count = substr_count($sTmp_Content, $sFile_Name);
            if ($iTmp_Count > 0) {
                $aName_Count[$sTmp_Path] = $iTmp_Count;
            }
        }
        return $aName_Count;
    }
    
    try {
        // 取得目錄名稱
        if ($cMisc->isEmpty($_GET['d'])) {
            throw new Exception('Directory name is empty');
        }
        $sDir_Name = $_GET['d'];
        
        // 目錄名稱檢查
        if (!preg_match('/[a-zA-Z0-9]/', $sDir_Name)) {
            throw new Exception('Invaild directory name.');
        }
        
        // 取得目錄檔案內容
        $aFile_Name_List = $cFile->recurcive_scan($sDir_Name);
        
        // 取得要搜尋的檔案
        $aSearch_File_Path = get_search_file($aFile_Name_List);
        
        // 取得要檢查的檔名
        $aSearch_File_Name = array();
        foreach ($aSearch_File_Path as $sTmp_Name) {
            $aSearch_File_Name[] = basename($sTmp_Name);
        }
        
        // 取得未出現過的檔名
        $aFile_Name_Count = array();
        foreach ($aSearch_File_Name as $sTmp_Name) {
            $aTmp_List = get_name_count($sTmp_Name, $aSearch_File_Path);
            if (count($aTmp_List) == 0) {
                $aFile_Name_Count[] = $sTmp_Name;
            }
        }
        echo '<pre>' . print_r($aFile_Name_Count, true) . '</pre>';
    } catch (Exception $e) {
        disp($e->getMessage());
    }
    
    include('../tpl/footer.php');
?>