<?php
    include('../tpl/header.php');
    include('../lib/html_common.inc');
    include('../lib/misc.inc');
    
    $key_len = 8;
    if (isset($_GET['l']) and $_GET['l'] > 0) {
        $key_len = $_GET['l'];
    }
    
    
    $M = new misc();
    $key = $M->rndKey($key_len);
    disp($key);
    include('../tpl/footer.php');
?>