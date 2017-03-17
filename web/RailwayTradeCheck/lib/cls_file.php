<?php
    class cls_file {
        function __construct() {
        }
        
        function recurcive_scan ($sAbs_Path) {
            $aScan_FIle_List = array();
            $aIgnore_File = array('.', '..');
            $aTmp_File_List = scandir($sAbs_Path);
            foreach ($aTmp_File_List as $sTmp_Name) {
                if (!in_array($sTmp_Name, $aIgnore_File)) {
                    $sTmp_Path = $sAbs_Path . '\\' . $sTmp_Name;
                    if (is_dir($sTmp_Path)) {
                        $aScan_FIle_List[$sTmp_Path][] = $this->recurcive_scan($sTmp_Path);
                    } else {
                        $aScan_FIle_List[] = $sTmp_Path;
                    }
                }
            }
            return $aScan_FIle_List;
        }
    }
?>