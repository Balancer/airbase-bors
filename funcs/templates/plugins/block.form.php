<?php
	function smarty_block_form($params, $content, &$smarty)
	{
		extract($params);
	
		if(empty($name))
		{
	        $smarty->trigger_error("form: empty parameter 'name'");
	        return;
		}
		
		if(empty($id))
			$id = NULL;
			
		$smarty->assign('current_form_class', class_load($name, $id));
		
		$uri = $GLOBALS['bors']->main_object()->uri();
		
		if($content == NULL)
		{
			if(empty($method))
				$method = 'post';

			if(empty($action))
				$action = $uri;
			if($action == 'this')
				$action = $GLOBALS['main_uri'];
				
			echo "<form";
			
			foreach(split(' ', 'action method name class style enctype') as $p)
				if(!empty($$p))
					echo " $p=\"{$$p}\"";
			
			echo ">\n";
			return;
		}
		
		echo $content;
		echo "<input type=\"hidden\" name=\"uri\" value=\"$uri\" />\n";
		if(!empty($ref))
			echo "<input type=\"hidden\" name=\"ref\" value=\"$ref\" />\n";

		if(!empty($action))
			echo "<input type=\"hidden\" name=\"action\" value=\"$action\" />\n";
		echo "<input type=\"hidden\" name=\"class_name\" value=\"$name\" />\n";
		if(!empty($id))
			echo "<input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
		echo "</form>\n";
	}
