<?
    function smarty_modifier_strftime($time, $mask)
    {
        if(!preg_match("!^\d+$!", $time))
            return $time;

        return strftime($mask, $time);
    }
?>