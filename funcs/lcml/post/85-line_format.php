<?php

    function lcml_line_format($txt)
    {
        if(empty($GLOBALS['lcml']['cr_type']))
            $cr_type = 'empty_as_para';
        else
            $cr_type = $GLOBALS['lcml']['cr_type'];

//        $txt .= "===$cr_type==<xmp>$txt</xmp>==";

        switch($cr_type)
        {
            case 'empty_as_para':   
                $txt = preg_replace("!(\n\n)!", "\n<p>", $txt); 
                break;
            case 'string_as_para':  
                $txt = preg_replace("!(^|\n)!", "\n<p>", $txt); 
                break;
            case 'dblstring_as_para':
                $txt = preg_replace("!(^|(\n\n\n))!", "\n<p>", $txt);
                $txt = preg_replace("!\n\n!", "<br />\n", $txt);
                $txt = preg_replace("!\n!", " ", $txt);
                break;
            case 'save_cr':
                $txt = preg_replace("!\n!", "<br /> ", $txt);
                break;
        }

        return $txt;
    }
?>
