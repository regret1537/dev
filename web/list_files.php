<?php
    include('../tpl/header.php');
    include('../lib/html_common.inc');
    include('../lib/misc.inc');
    
    $misc = new misc();
    
    try {
        # 取得目錄名稱
        if ($misc->isEmpty($_GET['d'])) {
            throw new Exception('Directory name is empty');
        }
        $dir_name = $_GET['d'];
        
        # 目錄名稱檢查
        if (!preg_match('/[a-zA-Z0-9]/', $dir_name)) {
            throw new Exception('Invaild directory name.');
        }
        
        # 取得顯示目錄名稱並檢查
        $disp_dir_name = '.';
        if (!$misc->isEmpty($_GET['dd'])) {
            $disp_dir_name = $_GET['dd'];
            if (!preg_match('/^((\\\\|\/){1}[a-zA-Z0-9_\-\.\s]+)+$/', $disp_dir_name)) {
                throw new Exception('Invaild display directory name.');
            }
        }        
        
        # 取得目錄檔案內容
        $src_dir_path = $_SERVER["DOCUMENT_ROOT"] . 'dev/file/' . $dir_name;
        $scan_file_list = array();
        recurciveScan($src_dir_path);
        
        # 顯示目錄檔案內容
        $disp_file_list = array();
        foreach ($scan_file_list['file'] as $file_path) {
            disp(str_replace($src_dir_path, $disp_dir_name , $file_path));
        }
    } catch (Exception $e) {
        disp($e->getMessage());
    }
    
    include('../tpl/footer.php');
    
    function recurciveScan ($abs_dir_path) {
        global $scan_file_list;
        $ignore_files = array('.', '..');
        $file_list = scandir($abs_dir_path);
        foreach ($file_list as $file_name) {
            if (!in_array($file_name, $ignore_files)) {
                $abs_file_path = $abs_dir_path . '/' . $file_name;
                if (is_dir($abs_file_path)) {
                    $scan_file_list['dir'][] = $abs_file_path;
                    recurciveScan($abs_file_path);
                } else {
                    $scan_file_list['file'][] = $abs_file_path;
                }
            }
        }
    }
?>