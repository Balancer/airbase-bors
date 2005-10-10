<?
    require_once("actions/edit-save.php");

    register_action_handler('edit-save', 'handler_edit_save');

    function handler_edit_save($uri, $action)
	{
//		echo "Test edit_save handler";

		foreach($_POST as $var=>$value)
			$$var = $value;
		
		action_edit_save($uri);

		// Показываем сохранную страницу
		show_page($uri);
		return true;
	}
?>
