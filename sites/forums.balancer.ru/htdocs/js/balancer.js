function tinymce_on()
{
	if(top.tinymce_state)
		return

	top.tinymce_state = true

//	$('textarea#text').each(function(ndx, el) {
//		tinyMCE.execCommand("mceAddControl", true, el);
//	})
//	tinyMCE.execCommand('mceToggleEditor', false, $('#text'));

	$('textarea#text').tinymce({
		script_url : "/_bors3rdp/tinymce-3.4.1jq/tiny_mce.js",
		theme : "advanced",
		mode : "none",
		plugins : "bbcode",
		theme_advanced_buttons1 : "bold,italic,underline,undo,redo,link,unlink,image,forecolor,styleselect,removeformat,cleanup,code",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "bottom",
		theme_advanced_toolbar_align : "center",
		theme_advanced_styles : "Code=codeStyle;Quote=quoteStyle",
		content_css : "css/bbcode.css",
		entity_encoding : "raw",
		add_unload_trigger : false,
		remove_linebreaks : false,
		inline_styles : false,
		convert_fonts_to_spans : false,
	})

	createCookie('bcet', 'tinymce', 365)
}

function tinymce_off()
{
	if(!top.tinymce_state)
		return

	top.tinymce_state = false

	if(!tinymce)
		return

	for (var i = 0; i < tinymce.editors.length; i++) {
		tinyMCE.execCommand("mceRemoveControl", true, tinymce.editors[i].id);
	}
}

function js_include(file_name, func)
{
	if(!top.js_include_loaded)
		js_include_loaded = []

	if(top.js_include_loaded[file_name])
		return

//	var script = document.createElement( 'script' );
//	script.type = 'text/javascript';
//	script.src = file_name;
//	s = '<script src="'+file_name+'"></script>'
//	$('head').append(s);
//	$('<script src="'+file_name+'"></script>').appendTo('head')
//	$(script).appendTo('head')
//	$('head').append(script)
//	document.body.appendChild(script);
//	alert(script)
	$.getScript(file_name, func)
	alert(file_name)
	top.js_include_loaded[file_name] = true
}

function ckeditor_on()
{
	if(top.ckeditor_state)
		return

	top.ckeditor_state = true

	js_include('/_bors3rdp/ckeditor-3.6/ckeditor.js');
	js_include('/_bors3rdp/ckeditor-3.6/adapters/jquery.js', function() {

	alert('loaded')
	$('#text').ckeditor({
		skin : 'v2',
		extraPlugins : 'bbcode',
		removePlugins : 'bidi,button,dialogadvtab,div,filebrowser,flash,format,forms,horizontalrule,iframe,indent,justify,liststyle,pagebreak,showborders,stylescombo,table,tabletools,templates',
		disableObjectResizing : true,
		fontSize_sizes : "30/30%;50/50%;100/100%;120/120%;150/150%;200/200%;300/300%",
		toolbar :
		[
			['Source', '-', 'Save','NewPage','-','Undo','Redo'],
			['Find','Replace','-','SelectAll','RemoveFormat'],
			['Link', 'Unlink', 'Image', 'Smiley','SpecialChar'],
			'/',
			['Bold', 'Italic','Underline'],
			['FontSize'],
			['TextColor'],
			['NumberedList','BulletedList','-','Blockquote'],
			['Maximize']
		],
		// Strip CKEditor smileys to those commonly used in BBCode.
		smiley_images :
		[
			'regular_smile.gif','sad_smile.gif','wink_smile.gif','teeth_smile.gif','tounge_smile.gif',
			'embaressed_smile.gif','omg_smile.gif','whatchutalkingabout_smile.gif','angel_smile.gif','shades_smile.gif',
			'cry_smile.gif','kiss.gif'
		],
		smiley_descriptions :
		[
			'smiley', 'sad', 'wink', 'laugh', 'cheeky', 'blush', 'surprise',
			'indecision', 'angel', 'cool', 'crying', 'kiss'
		]
	})

	createCookie('bcet', 'ckeditor', 365)

	})
}

function ckeditor_off()
{
	if(!top.ckeditor_state)
		return

	top.ckeditor_state = false
	$('#text').ckeditor(function(){ this.destroy(); })
}

function markitup_on()
{
	if(top.markitup_state)
		return

	top.markitup_state = true

    $('#text').markItUp(myBbcodeSettings);
    $('#emoticons a').click(function() {
        emoticon = $(this).attr("title");
        $.markItUp( { replaceWith: ' '+emoticon+' ' } )
        return false;
    })

	createCookie('bcet', 'markitup', 365)
}

function markitup_off()
{
	if(!top.markitup_state)
		return

	top.markitup_state = false
    $('#text').markItUpRemove()
}

function turn_textarea()
{
	tinymce_off()
	markitup_off()
	ckeditor_off()

	createCookie('bcet', 'textarea', 365)
	return false
}

function turn_tinymce()
{
	markitup_off()
	ckeditor_off()
	tinymce_off()
	tinymce_on()
	return false
}

function turn_markitup()
{
	tinymce_off()
	ckeditor_off()
	markitup_off()
	markitup_on()
	return false
}

function turn_ckeditor()
{
	tinymce_off()
	markitup_off()
	ckeditor_off()
	ckeditor_on()
	return false
}

$().ready(function() {
	t = readCookie('bcet', 'markitup')
	if(t)
		eval('turn_'+t+'()')
})
