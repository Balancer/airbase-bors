<?
    register_action_handler('post-delete', 'handler_action_post_delete');

    function handler_action_post_delete($uri, $action)
	{
		$us = new User;

		if(!$us->data('id'))
		{
			$GLOBALS['page_data']['title'] = "������";
			$GLOBALS['page_data']['source'] = '�� �� ����� � �������.';

			show_page($uri);
			return true;
		}

		if(!access_allowed($uri))
		{
			$GLOBALS['page_data']['title'] = "������";
			$GLOBALS['page_data']['source'] = '� ��� ������������ ���� ��� ���������� ��������';

			show_page($uri);
			return true;
		}

		$GLOBALS['page_data']['title'] = "�������� ���������";
		
		$hts = new DataBaseHTS();

		$message = $hts->get_data($uri, 'source');
		
		$GLOBALS['page_data']['source'] = <<< __EOT__
[big][red]��������! �� ��������� ������� ���������:[/red][/big]
[pre]{$message}[/pre]
�� �������? ����� �������� ���� ����� ���������� ������������!

[center][b][big][$uri|���] | [$uri?post-delete-do|��][/big][/b][/center]
__EOT__;

		show_page($uri);

		return true;
	}
?>
