<?
    function lp_flash($url,$params)
    {
        list($width,$height)=split("x",(isset($params['size'])?$params['size']:"")."x");
        if(!$width)  $width=468;
        if(!$height) $height=351;
        return "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" width=$width height=$height><param name=movie value=$url><param name=play value=true><param name=loop value=true><param name=quality value=high><embed src=$url width=$width height=$height play=true loop=true quality=high></embed></object>";
    }
?>