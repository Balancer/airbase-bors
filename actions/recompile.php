<?
    require_once("funcs/DataBaseHTS.php");
    require_once("funcs/Cache.php");
    require_once("funcs/CacheStaticFile.php");
    require_once("funcs/lcml.php");
    require_once("funcs/templates/smarty.php");

    function recompile($uri, $update_parents = true)
    {
		$params['cache_disable'] = true;
	
		if(empty($GLOBALS['cms']['recompiled_uris']))
			$GLOBALS['cms']['recompiled_uris'] = array();
		
        if(empty($uri))
            return;

		if(!empty($GLOBALS['cms']['recompiled_uris'][$uri]))
			return;
			
        $ch = &new Cache();
        $ch->clear_by_id($uri);
        $ch->clear_by_uri($uri);

		if(!empty($GLOBALS['cms']['cache_static']))
		{
			$sf = &new CacheStaticFile($uri);
			$sf->save(show_page($uri, false));
		}
		
        $hts = &new DataBaseHTS;

		$source = $hts->get_data($uri, 'source');
		$body   = $hts->get_data($uri, 'body');

//		exit("<xmp>$body</xmp>");

        if($source || $body)
        {
			$ch->clear('lcml-compiled', md5($source));

            $hts->set_data($uri, 'compile_time', time());

            if(!empty($GLOBALS['cms_images']))
            {
                foreach($GLOBALS['cms_images'] as $img)
                    $out .= "<li>$img:&nbsp;<input type=\"hidden\" name=\"upload_names[]\" value=\"$img\"><input type=\"file\" size=\"10\" name=\"upload_file[]\">\n";
                if($out)
                    $out = <<<__EOT__
<form action="/admin/upload.php" method="POST" enctype="multipart/form-data">
<dl class="box">
<dt>Files load</dt>
<dd>
<div align="left"><ul>$out</ul></div>
<input type="submit" value="Load">
</dd></dl>
<input type="hidden" name="page" value="$uri">
</form>
__EOT__;
                $GLOBALS['cms_right_column'][] = $out;
            }

            $hts->set_data($uri, 'right_column', !empty($GLOBALS['cms_right_column']) ? join("\n",$GLOBALS['cms_right_column']) : NULL);
        }
        else
        {
            debug(__FILE__."[".__LINE__."] Not found page_id for '$uri'!");
//            exit(ec("Не найден идентификатор страницы '$uri'!"));
        }

		// Перекомпилируем родителей
		foreach($hts->get_data_array($uri, 'parent') as $parent)
		{
			$GLOBALS['cms']['recompiled_uris'][$parent] = $uri;
			
			recompile($parent);
		}

		// Чтобы не пришлось перекомпилировать вообще всю систему 
		// (через родителей потом ко всех их потомкам)
		if(!$update_parents)
			return;
			
		// Перекомпилируем детей. На тему имени навигации
		foreach($hts->get_data_array($uri, 'child') as $child)
		{
			$GLOBALS['cms']['recompiled_uris'][$child] = $uri;
			
			recompile($child, false);
		}
	}
?>
