<?
    function colorer($txt, $type)
    {
        $type = trim($type);

        list($type, $color)=preg_split("!\s+!",$type.' ');


//		if(@$GLOBALS['lcml']['forum_type'] == 'punbb' && !$color)
//			$color = 'black';

        if(trim($color))
        {
            $color="-i $color";
        }

        if(empty($type))
            $type='text';

        if($type)
        {
/*            $txt=preg_replace("!&lt;br&gt;!","\n",$txt);
            $txt=preg_replace("!&lt;br .*?&gt;!","\n",$txt);
            $txt=preg_replace("!&lt;p&gt;!","\n\n",$txt);
            $txt=preg_replace("!&lt;p .*?&gt;!","\n\n",$txt);
            $txt=preg_replace("!&lt;/p&gt;!","\n",$txt);*/

/*            $ext=array(
                'perl'=>'pl',
                'c++'=>'cpp',
                'cs'=>'csharp',
                'for'=>'fortran',
            );

            if($ext[$type])
                $type=$ext[$type];*/

            $tmp_file="/tmp/".rand().".$type";
            $fh=fopen($tmp_file,"w");
            $txt=strtr($txt,array(
                '&#33;'=>"!",
                '&#39;'=>"'",
                '&#036;'=>"\$",
                '&#092;'=>"\\",
                '&#124;'=>"|",
                '&amp;'=>"&",
                ));
//            fwrite($fh,html_entity_decode($txt, ENT_COMPAT, 'UTF-8'));
            fwrite($fh, $txt);
            fclose($fh);
            $q = "/usr/bin/colorer ".escapeshellcmd($color)." -h -dh -ei utf-8 -t ".escapeshellcmd($type)." -c /usr/share/colorer/catalog.xml ".escapeshellcmd($tmp_file)." 2> /dev/null";

            $txt_in = trim(substr(`$q`,1));

            if($txt_in)
                $txt = $txt_in;

/*            $txt=strtr($txt,array(
                "!"=>"&#33;",
//              "'"=>"&#39;",
                "\$"=>"&#036;",
                "\\"=>"&#092;",
                "|"=>"&#124;",
                ));*/
				
            unlink($tmp_file);
        }
		
        return $txt;
    }
