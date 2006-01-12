<?
    function lp_red($txt)   { return '<font color="red">'.lcml($txt).'</font>';}
    function lp_green($txt) { return '<font color="green">'.lcml($txt).'</font>';}
    function lp_blue($txt)  { return '<font color="blue">'.lcml($txt).'</font>';}
    function lp_color($txt, $params)  { return "<span style=\"color: {$params['name']}\">".lcml($txt)."</span>";}
?>
