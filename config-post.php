<?php

template_jquery();
template_jquery_plugin('tageditor/jquery.tag.editor.js');
template_jquery_plugin('jquery.lazyload-ad-1.4.min.js');
//template_jquery_plugin('jquery.lazyload.js'); — тормозит скроллинг

template_js_include('/_bors/js/funcs3.js?');
//template_js_include('/_bors/js/tune.js');
template_js_include('/_bors/js/cfuncs.js');
template_js_include('/_bal/js/common.js?');
template_js_include('/_bors/js/bors-jquery.js');

config_set('locked_db', @file_get_contents('/tmp/mysqldump.lock'));

template_jquery_cloud_zoom();
template_jquery_hoverZoom();


