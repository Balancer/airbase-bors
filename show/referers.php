<?
    @header('Content-Type: text/html; charset=utf-8');
    @header('Content-Language: ru');
    ini_set('default_charset','utf-8');
    setlocale(LC_ALL, "ru_RU.utf8");

	require_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
    require_once("funcs/DataBase.php");
    require_once("funcs/DataBaseHTS.php");
    require_once("funcs/Cache.php");

    referers_add();
    referers_show();

    function referers_add($uri=NULL)
    {
        if(empty($_SERVER['HTTP_REFERER']))
            return;

        if(!$uri)
            $uri=$GLOBALS['PHP_SELF'];

        $dbh = new DataBase('HTS');
        $hts = new DataBaseHTS();
        $id  = $hts->page_id_by_uri($uri);
        $ref = $hts->page_id_by_uri($_SERVER['HTTP_REFERER']);

        $dbh->query("INSERT INTO `hts_ext_referers` 
            SET `id` = $id, `referer` = '$ref', `count`=1, `first_enter`=UNIX_TIMESTAMP(), `last_enter`=UNIX_TIMESTAMP()
            ON DUPLICATE KEY UPDATE `count` = `count` + 1, `last_enter`=UNIX_TIMESTAMP()");
    }

    function referers_show($uri=NULL)
    {
        if(!$uri)
            $uri=$GLOBALS['PHP_SELF'];

        $ch = new Cache();
        $ch->clear_check('ext_referers',$uri,900);

        if($ch->get('ext_referers',$uri))
        {
            echo $ch->last();
            return;
        }

        $dbh = new DataBase('HTS');
        $hts = new DataBaseHTS();
        $out = NULL;

        $id  = $hts->page_id_by_uri($uri);
        $refs = $dbh->get_array("SELECT * FROM `hts_ext_referers` WHERE `id`=$id ORDER BY `count` DESC LIMIT 0,20");
        if(is_array($refs))
        {
            foreach($refs as $r)
            {
                $ref = $hts->page_uri_by_id($r['referer']);
                $title = $hts->get_data($r['referer'],'title');
                if($ref)
                    $out .= "<li><a href=\"$ref\">".($title?$title:$ref)."</a> <small>[Переходов: {$r['count']}]</small>\n";
            }
            if($out)
            {
                $out  = "<dl class=\"box\"><dt>Ссылки на эту страницу</dt><dd><ul>\n$out";
                $out .= "</ul></dd></dl>\n";
            }
        }

        echo $out;
        $ch->set('ext_referers',$uri,$out);
    }

?>
