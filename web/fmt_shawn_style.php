<?php
    include('../tpl/header.php');
    include('../lib/html_common.inc');
    include('../lib/misc.inc');
    
    $src_str = 'update o_auth set close=1,clsamt=clsamt+$amount where remsg="已授權" and sr="$sel_ary[0]" limit 1 ';
    $src_list = array(
        'select', 'insert', 'update', 'delete', 'from', 'set', 'where', 'and', 'limit', '"', '\''
    );
    $replace_list = array(
        'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'FROM', 'SET', 'WHERE', 'AND', 'LIMIT', '\'', '"'
    );
    $replaced_str = str_replace($src_list, $replace_list , $src_str);
    
    disp($replaced_str);
    
    include('../tpl/footer.php');
?>