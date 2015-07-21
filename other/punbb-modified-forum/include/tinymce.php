<?php

//if(($profile = config('client_profile')) && $profile->textarea_type() != 'markitup')

if($profile = config('client_profile'))
	$profile = $profile->textarea_type();

if($profile == 'textarea')
	return;

global $header;
global $footer;

if($profile == 'wysibb')
{
	wysibb::appear("#bbcode");

	$jsinc = bors_page::template_data('js_include');
	if($jsinc)
		foreach($jsinc as $j)
			$header[] = "<script type=\"text/javascript\" src=\"{$j}\"></script>\n";

	if($happ = bors_page::template_data('head_append'))
		foreach($happ as $h)
			$header[] = $h."\n";

	if($jss = bors_page::template_data('js_include_post'))
		foreach($jss as $js)
			$footer[] = "<script type=\"text/javascript\" src=\"{$js}\"></script>\n";

	if($code = bors_page::template_data('jquery_document_ready'))
	{
		$footer[] = "<script type=\"text/javascript\"><!--\n\$(document).ready(function(){\n";
		foreach($code as $c)
			$footer[] = "$c\n";
		$footer[] = "})\n--></script>\n";
	}

	return;
}

//var_dump(@$client_profile);


// template_jquery_markitup('#bbcode', 'myBbcodeSettings');

$markitup_base = config('jquery.markitup.base');
$markitup_sets = config('jquery.markitup.sets.bbcode');

$header[] = <<< __EOT__
<link rel="stylesheet" type="text/css" href="$markitup_base/skins/simple/style.css" />
<link rel="stylesheet" type="text/css" href="$markitup_sets/style.css" />

<script type="text/javascript" src="$markitup_base/jquery.markitup.js"></script>
<script type="text/javascript" src="$markitup_sets/set.js"></script>

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

