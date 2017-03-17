<?php
    abstract class ParamType {
        const GET = '_GET';
        const POST = '_POST';
    }

    function disp($cont, $is_pre = false) {
        if ($is_pre) {
            echo '<pre>' . $cont . '</pre><br />';
        } else {
            echo $cont . '<br />';
        }
    }
    
    function genSimpleForm($act, $params, $tar = '_self', $mhd = 'POST') {
        $form_html = genHtml('<form action="' . $act . '" method="' . $mhd . '" target="' . $tar . '">', 2);
        foreach($params as $mem_name => $mem_val) {
            if (gettype($mem_val) == 'array') {
                foreach ($mem_val as $ary_val) {
                    $form_html .= genHtml($mem_name . ' : <input type="text" name="' . $mem_name . '[]" value="' . $ary_val . '"><br />', 4);
                }
            } else {
                $form_html .= genHtml($mem_name . ' : <input type="text" name="' . $mem_name . '" value="' . $mem_val . '"><br />', 4);
            }
            
        }
        $form_html .= genHtml('<input type="submit" value="送出">', 4);
        $form_html .= genHtml('</form>', 2);
        
        return $form_html;
    }
    
    function genHtml($cont, $sp_num) {
        return str_repeat(' ', $sp_num) . $cont . "\n";
    }
    
    function setHtml($cont, $sp_num) {
        echo genHtml($cont, $sp_num);
    }
    
    function getParam($param_name, $param_type = ParamType::GET) {
        $param_val = '';
        switch ($param_type) {
            case ParamType::POST:
                $param_val = htmlspecialchars($_POST[$param_name]);
                break;
            case ParamType::GET:
                $param_val = htmlspecialchars($_GET[$param_name]);
                break;
            default:
        }
        return $param_val;
    }
?>