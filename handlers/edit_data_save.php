<?
    require_once('actions/edit-data-save.php');

    register_action_handler('edit-data-save', 'handler_edit_data_save');

    function handler_edit_data_save($uri, $action)
	{
		actions_edit_data_save($uri);
		
		echo "Can't save data for ".$uri;
		return false;
	}
?>
