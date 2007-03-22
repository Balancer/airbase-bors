<?
    require_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
    require_once("funcs/modules/translit.php");

    function translite_uri($uri)
    {
        $uri = strtolower($uri);

        $uri = to_translit($uri);

        $uri = strtr($uri, array(
        ' ' => '_', 
        '!' => '_exclm_', 
        '"' => '_dquot_', 
        '#' => '_sharp_', 
        '$' => '_doll_', 
        '%' => '_prcnt_', 
        '&' => '_amp_', 
        '\''=> '_quote_', 
        '('=> '_lbrck_', 
        ')'=> '_rbrck_', 
        '*'=> '_mult_', 
        '+' => '_plus_',
        ',' => '_comma_',
        '.' => '_dot_',
        '/' => '_slash_',
        ':' => '_colon_',
        ';' => '_smcln_',
        '<' => '_lt_',
        '=' => '_eq_',
        '>' => '_gt_',
        '?' => '_quest_', 
        '@' => '_at_', 
        '['=> '_lsbrc_', 
        "\\" => '_bkslsh_',
        ']'=> '_rsbrc_', 
        '^'=> '_power_', 
        '`'=> '_gracc_', 
        '{'=> '_lcbrc_', 
        '|'=> '_vertl_', 
        '}'=> '_rcbrc_', 
        '~'=> '_tild_', 
        ));

        $uri = rawurlencode($uri);

        $uri = str_replace('%','_',$uri);
        return $uri;        
    }

    function translite_path($path)
    {
        $path = to_translit($path);

        $path = strtr($path, array(
        ' ' => '_', 
        '!' => '_exclm_', 
        '"' => '_dquot_', 
        '#' => '_sharp_', 
        '%' => '_prcnt_', 
        '&' => '_amp_', 
        '\''=> '_quote_', 
        '*'=> '_mult_', 
        ':' => '_colon_',
        '<' => '_lt_',
        '>' => '_gt_',
        '?' => '_quest_', 
        '['=> '_lsbrc_', 
        "\\" => '_bkslsh_',
        ']'=> '_rsbrc_', 
        '^'=> '_power_', 
        '`'=> '_gracc_', 
        '|'=> '_vertl_', 
        ));

        return $path;        
    }

    function translite_uri_simple($uri)
    {
//        $uri = strtolower($uri);

        $uri = to_translit($uri);

        $uri = strtr($uri, array(
        ' ' => '-', 
		':' => '-',
        '"' => "'", 
        '#' => '-N',
        '&' => '-and-',
        '+' => '-plus-',
        '`' => "'",
        '/' => '-',
        '<' => '-less-',
        '=' => '-eq-',
        '>' => '-great-',
        '?' => '-', 
        "\\" => '-',
        '|'=> '!', 
        ));

//        $uri = rawurlencode($uri);
//        $uri = str_replace('%','_',$uri);
        $uri = preg_replace("!^\-+!",'', $uri);
        $uri = preg_replace("!\-+$!",'', $uri);
        $uri = preg_replace("!\-{2,}!",'-', $uri);
        $uri = preg_replace("!(,|\.)-!",'$1', $uri);
        return $uri;        
    }
