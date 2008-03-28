<?
    register_handler('!^(http://[^/]+)/cache.*/\d*x\d*/.*(\.png|\.gif|\.jpe?g|)$!i', 'handler_cached_images');

    function handler_cached_images($uri, $m=array())
	{
		include("tools/cache-image-resize.php");
		return true;
    }
