{form act="post" form_template_class="bors_forms_templates_bootstrap"}
{textarea id="editor" name="text" value="" css="span12" cols="60" rows="20"}<br/>

<div class="alert">
	<small><span class="text-error"><strong>Важно!</strong></span> Размещая на
	форуме информацию, авторами которой Вы <b>не</b> являетесь, Вы
	обязуетесь нести всю ответственность за соблюдение прав авторов этой
	информации. В случае претензий правообладателей эта информация может
	быть удалена. Все материалы, автором которых являетесь Вы, при
	опубликовании на форумах приобретают лицензию
	<a href="http://www.balancer.ru/support/2009/02/t66269--Prava-publikatsii-avtorskikh-materialov-.html"><b>Creative Commons</b> (by-nc-sa)</a>.
	</small>
</div>

{submit value="Отправить сообщение" type="button" css="btn btn-primary"}
{submit value="Предварительный просмотр" type="button" css="btn"}

<br/>
<br/>

<div class="accordion" id="post-options">
	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#post-options" href="#collapseOptions">
				Дополнительные настройки
			</a>
		</div>
		<div id="collapseOptions" class="accordion-body collapse">
			<div class="accordion-inner">
				{checkbox name="is_subscribe" checked=true label="Подписаться на тему и получать извещения об ответах" br=" "}
				{checkbox name="is_blog" checked=false label="Разместить ответ в Вашем блоге" br=" "}
				{checkbox name="is_moderatorial" checked=false label="Данное сообщение — модераториал" br=" "}

	<div class="well">
		<b>Выбор редактора сообщений:</b>
    	<div class="btn-group" data-toggle="buttons-radio">
	    <button type="button" class="btn btn-primary" id="turn-wysibb-on">WysiBB</button>
    	<button type="button" class="btn btn-primary" id="turn-markitup-on">markItUp!</button>
	    <button type="button" class="btn btn-primary" id="turn-textarea-on">textarea</button>
    	</div>
	</div>

			</div>
		</div>
	</div>
</div>

{/form}

<div class="accordion" id="post-attaches">
	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#post-attaches" href="#collapseAttaches">
				Вложения (аттачи)
			</a>
		</div>
		<div id="collapseAttaches" class="accordion-body collapse">
			<div class="accordion-inner">

<!-- Bootstrap styles for responsive website layout, supporting different screen sizes -->
<link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap-responsive.min.css">
<!-- Bootstrap CSS fixes for IE6 -->
<!--[if lt IE 7]><link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap-ie6.min.css"><![endif]-->
<!-- Bootstrap Image Gallery styles -->
<link rel="stylesheet" href="http://blueimp.github.com/Bootstrap-Image-Gallery/css/bootstrap-image-gallery.min.css">
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="http://www.balancer.ru/_bors-3rd/bower/components/jquery-file-upload/css/jquery.fileupload-ui.css">

    <form id="fileupload" action="/file-upload/" method="POST" enctype="multipart/form-data">
        <!-- Redirect browsers with JavaScript disabled to the origin page -->
        <noscript><input type="hidden" name="redirect" value="/"></noscript>
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="span7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="icon-plus icon-white"></i>
                    <span>Добавить файлы...</span>
                    <input type="file" name="files[]" multiple>
                </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="icon-upload icon-white"></i>
                    <span>Начать загрузку</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="icon-ban-circle icon-white"></i>
                    <span>Отменить загрузку</span>
                </button>
                <button type="button" class="btn btn-danger delete">
                    <i class="icon-trash icon-white"></i>
                    <span>Удалить</span>
                </button>
                <input type="checkbox" class="toggle">
            </div>
            <!-- The global progress information -->
            <div class="span5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="bar" style="width:0%;"></div>
                </div>
                <!-- The extended global progress information -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <!-- The loading indicator is shown during file processing -->
        <div class="fileupload-loading"></div>
        <br>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
    </form>
{literal}
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td>
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td>{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary start">
                    <i class="icon-upload icon-white"></i>
                    <span>Start</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td>{% if (!i) { %}
            <button class="btn btn-warning cancel">
                <i class="icon-ban-circle icon-white"></i>
                <span>Cancel</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        <td>
            <button class="btn btn-danger delete" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                <i class="icon-trash icon-white"></i>
                <span>Delete</span>
            </button>
            <input type="checkbox" name="delete" value="1" class="toggle">
        </td>
    </tr>
{% } %}
</script>
{/literal}

<script src="http://www.balancer.ru/_bors-3rd/bower/components/jquery-file-upload/js/vendor/jquery.ui.widget.js"></script>
<script src="http://blueimp.github.com/JavaScript-Templates/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="http://blueimp.github.com/JavaScript-Load-Image/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="http://blueimp.github.com/JavaScript-Canvas-to-Blob/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS and Bootstrap Image Gallery are not required, but included for the demo -->
<script src="http://blueimp.github.com/cdn/js/bootstrap.min.js"></script>
<script src="http://blueimp.github.com/Bootstrap-Image-Gallery/js/bootstrap-image-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="http://www.balancer.ru/_bors-3rd/bower/components/jquery-file-upload/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="http://www.balancer.ru/_bors-3rd/bower/components/jquery-file-upload/js/jquery.fileupload.js"></script>
<!-- The File Upload file processing plugin -->
<script src="http://www.balancer.ru/_bors-3rd/bower/components/jquery-file-upload/js/jquery.fileupload-fp.js"></script>
<!-- The File Upload user interface plugin -->
<script src="http://www.balancer.ru/_bors-3rd/bower/components/jquery-file-upload/js/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<script src="http://www.balancer.ru/_bal/file-upload.js"></script>



			</div>
		</div>
	</div>
</div>

