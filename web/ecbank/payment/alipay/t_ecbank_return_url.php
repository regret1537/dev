<?php
    define('WEB_ROOT', 'C:/wamp/www/dev');
    include(WEB_ROOT . '/tpl/header.php');
    include(WEB_ROOT . '/lib/html_common.inc');
    include(WEB_ROOT . '/lib/misc.inc');

    disp('_GET');
    disp($_GET);
    
    disp('_POST');
    disp($_POST);
    
    include(WEB_ROOT . '/tpl/footer.php');
?>