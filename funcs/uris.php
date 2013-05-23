<?php
    function uri2path($uri)
    {
        return preg_replace("!http://[^/]+!", "", $uri);
    }
