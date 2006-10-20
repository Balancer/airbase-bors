<?
    register_action('attach-upload', 'handler_action_attach_upload');

    function handler_action_attach_upload($uri, $action)
	{
		require_once("funcs/upload.php");
		upload($uri);

		go($uri);

		return true;
	}
?>
