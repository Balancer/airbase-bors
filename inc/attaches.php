<?php

	function attach_get_extension($filename='')
	{
		if(strlen($filename)==0)
			return '';

		return strtolower(ltrim(strrchr($filename,"."), "."));
	}

	function attach_create_mime($extension='')
	{
		$mimecodes = array (						// mimes taken from microsoft ... those that don't need external programs to work
												// abit unsure on some file extensions, but they aren't used so much anyhow :P
						//fileext.				mimetype
						'rtf' 			=>		'text/richtext',
						'html'			=>		'text/html',
						'htm'			=>		'text/html',
						'aiff'			=>		'audio/x-aiff',
						'iff'			=>		'audio/x-aiff',
						'basic'			=>		'audio/basic',  // no idea about extension
						'wav'			=>		'audio/wav',
						'gif'			=>		'image/gif',
						'jpg'			=>		'image/jpeg',
						'jpeg'			=>		'image/pjpeg',
						'tif'			=>		'image/tiff',
						'png'			=>		'image/x-png',
						'xbm'			=>		'image/x-xbitmap',  // no idea about extension
						'bmp'			=>		'image/bmp',
						'xjg'			=>		'image/x-jg',  // no idea about extension
						'emf'			=>		'image/x-emf',  // no idea about extension
						'wmf'			=>		'image/x-wmf',  // no idea about extension
						'avi'			=>		'video/avi',
						'mpg'			=>		'video/mpeg',
						'mpeg'			=>		'video/mpeg',
						'ps'			=>		'application/postscript',
						'b64'			=>		'application/base64',  // no idea about extension
						'macbinhex'		=>		'application/macbinhex40',  // no idea about extension
						'pdf'			=>		'application/pdf',
						'xzip'			=>		'application/x-compressed',  // no idea about extension
						'zip'			=>		'application/x-zip-compressed',
						'gzip'			=>		'application/x-gzip-compressed',
						'java'			=>		'application/java',
						'msdownload'	=>		'application/x-msdownload'  // no idea about extension
						);

		foreach ($mimecodes as $type => $mime ){
			if($extension==$type)
				return $mime;
		}
		return 'application/octet-stream';	// default, if not defined above...
	}
