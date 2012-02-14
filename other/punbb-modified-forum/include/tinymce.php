<?php

if(($profile = config('client_profile')) && $profile->textarea_type() != 'markitup')
	return;

//var_dump(@$client_profile);

global $header;

$header[] = <<< __EOT__
<link rel="stylesheet" type="text/css" href="/_bors3rdp/jquery/plugins/markitup/skins/simple/style.css" />
<link rel="stylesheet" type="text/css" href="/_bors3rdp/jquery/plugins/markitup/sets/bbcode/style.css" />

<script type="text/javascript" src="/_bors3rdp/jquery/jquery.js"></script>
<script type="text/javascript" src="/_bors3rdp/jquery/plugins/markitup/jquery.markitup.js"></script>
<script type="text/javascript" src="/_bors3rdp/jquery/plugins/markitup/sets/bbcode/set.js"></script>

<script language="javascript">
$(document).ready(function()	{
    $('#bbcode').markItUp(myBbcodeSettings);

    $('#emoticons a').click(function() {
        emoticon = $(this).attr("title");
        $.markItUp( { replaceWith: ' '+emoticon+' ' } );
        return false;
    });
});
</script>
__EOT__;
