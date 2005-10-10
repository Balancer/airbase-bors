<?php

    function lcml_line_format($txt)
    {
        //echo "##$txt##<br>";
        // Если перевод строк обрабатывает движок форума, то ничего не делаем
        if(!empty($GLOBALS['ibforums']))
            return $txt;

//        if(empty($GLOBALS['page']))
//            return $txt;

        if(empty($GLOBALS['lcml']['cr_type']))
            $cr_type = 'empty_as_para';
        else
            $cr_type = $GLOBALS['lcml']['cr_type'];

//        echo "===$cr_type===";

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
                $txt = preg_replace("!\n!", "<br />\n", $txt);
                break;
        }

        return $txt;
    }
?>
