<?php
$param = $_REQUEST['param'];
If (strlen($param) < 17 && stripos($param, 'eval') === false && stripos($param, 'assert') === false) {
    eval($param);
}
