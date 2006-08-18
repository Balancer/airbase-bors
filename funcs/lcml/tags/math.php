<?
    define( "MW_MATH_PNG",    0 );
    define( "MW_MATH_SIMPLE", 1 );
    define( "MW_MATH_HTML",   2 );
    define( "MW_MATH_SOURCE", 3 );
    define( "MW_MATH_MODERN", 4 );
    define( "MW_MATH_MATHML", 5 );

    function wfStrencode($s){return '';}
    function wfQuery($s){return '';}
    function wffetchobject(){return '';}
    function wfdebug($s)
    {
        $fh = fopen('/home/airbase/forums/math.log','at');
        fwrite($fh, $s."\n");
        fclose($fh);
    }
    function wfmsg($s)
    {
        switch($s)
        {
            case 'math_failure':            return 'Невозможно разобрать выражение';
            case 'math_unknown_error':      return 'неизвестная ошибка';
            case 'math_unknown_function':   return 'неизвестная функция';
            case 'math_lexing_error':       return 'лексическая ошибка';
            case 'math_syntax_error':       return 'синтаксическая ошибка';
        }
        return 'непонятная ошибка, зови админа!';
    }

    class tmpwgUser
    {
        var $mode;
        function getOption($type)
        {
            return isset($this->mode) ? $this->mode : 0;
        }
    }

    function lp_math($text,$params)
    {
        $GLOBALS['wgMathDirectory']='/home/airbase/forums/images/math';
        $GLOBALS['wgTmpDirectory']='/tmp';
        $GLOBALS['wgInputEncoding']='utf-8';
        $GLOBALS['wgTexvc']='/var/www/wiki.airbase.ru/htdocs/math/texvc';
        $GLOBALS['wgMathPath']='http://forums.airbase.ru/images/math';
        $GLOBALS['wgUser'] = new tmpwgUser;

        $text = str_replace(array('&#092;','&#33;'), array("\\","!"), $text);

        switch($params['orig'])
        {
            case 'png': $GLOBALS['wgUser']->mode = 0; break;
            case 'simple': $GLOBALS['wgUser']->mode = 1; break;
            case 'html': $GLOBALS['wgUser']->mode = 2; break;
            case 'source': $GLOBALS['wgUser']->mode = 3; break;
            case 'modern': $GLOBALS['wgUser']->mode = 4; break;
            case 'mathml': $GLOBALS['wgUser']->mode = 5; break;
            default: $GLOBALS['wgUser']->mode = 0;
        }

        error_reporting(E_ALL & ~E_NOTICE);
        include_once("/var/www/wiki.airbase.ru/htdocs/includes/Math.php");
        $text = renderMath($text);
        error_reporting(E_ALL);

        $text = str_replace(array("\\","!"), array('&#092;','&#33;'), $text);
        $text = preg_replace("!alt=\"(.+?)\"!","alt=\"$1\" title=\"$1\"", $text);
        
        wfdebug($text);
        return $text;
    }
?>