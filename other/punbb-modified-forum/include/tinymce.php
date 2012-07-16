<?php

if(($profile = config('client_profile')) && $profile->textarea_type() != 'markitup')
	return;

//var_dump(@$client_profile);

global $header;

// template_jquery_markitup('#bbcode', 'myBbcodeSettings');

$markitup_base = config('jquery.markitup.base');
$markitup_sets = config('jquery.markitup.sets.bbcode');

$header[] = <<< __EOT__
<link rel="stylesheet" type="text/css" href="/_bors3rdp/jquery/plugins/$markitup_base/skins/simple/style.css" />
<link rel="stylesheet" type="text/css" href="/_bors3rdp/jquery/plugins/$markitup_sets/style.css" />

<script type="text/javascript" src="/_bors3rdp/jquery/jquery.js"></script>
<script type="text/javascript" src="/_bors3rdp/jquery/plugins/$markitup_base/jquery.markitup.js"></script>
<script type="text/javascript" src="/_bors3rdp/jquery/plugins/$markitup_sets/set.js"></script>

<script language="javascript">
$(document).ready(function()	{
	$('#bbcode').markItUp(mySettings);
    $('#emoticons a').click(function() {
        emoticon = $(this).attr("title");
        $.markItUp( { replaceWith: ' '+emoticon+' ' } );
        return false;
    });
});
</script>
__EOT__;

