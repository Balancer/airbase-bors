<?
    function lp_php($txt,$params)
    {
        if(!check_lcml_access('usephp',false))
            return $txt;

        $txt = save_format($txt);
        return "<?php $txt ?>";
    }
?>
