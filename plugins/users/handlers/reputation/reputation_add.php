<?
    register_action('reputation_add_do', 'plugins_users_reputation_add_do');

    function plugins_users_reputation_add_do($uri, $action)
	{
		require_once('funcs/modules/messages.php');

		$uid = intval($_POST['user_id']);
		if(!$uid)
			return error_message(ec("Не задан ID пользователя."));

		$me = &new User();
		$dbf = &new DataBase('punbb');
		$dbu = &new DataBase('USERS');
		$me_id = $me->get('id');
		
		if($me_id == 1)
			return error_message(ec("Голосование возможно только для авторизованных пользователей."));

		if($me_id == $uid)
			return error_message(ec("Нельзя ставить репутацию самому себе."));
		
		if($dbf->get("SELECT num_posts FROM users WHERE id=$me_id") < 50)
			return error_message(ec("Репутацию выставлять могут только участники, имеющие более 50 сообщений на форуме."));

		$dbu->insert('reputation_votes', array(
			'user_id'		=> $uid,
			'time'			=> time(),
			'score'			=> $_POST['score'] > 0 ? 1 : -1,
			'voter_id'		=> $me_id,
			'uri'			=> $_POST['uri'],
			'comment'		=> $_POST['comment'],
		));

		$grw = array(
				1 => 8, // admin
				2 => 6, // moder
				3 => 0, // guest
				5 => 4, // coordin
				6 => 2, // старожилы
				21 => 4, // координатор-литератор
			);

		$total = 0;
		foreach($dbu->get_array("SELECT voter_id as id, SUM(score) as sum FROM `reputation_votes` WHERE user_id = $uid GROUP BY voter_id") as $v)
		{
			$reput = (atan($dbf->get("SELECT reputation FROM users WHERE id={$v['id']}"))*2/pi() + 1)/2;
			$group = $dbf->get("SELECT group_id FROM users WHERE id={$v['id']}");
				
			$weight = @$grw[$group];
			if(!$weight)
				$weight = 1;

			if($v['id'] == 10000)
				$weight = 10;
					
			if($dbf->get("SELECT num_posts FROM users WHERE id={$v['id']}") < 50)
				$weight = 0;
				
			$sum = atan($v['sum'])*2/pi() * $weight * $reput;
			$total += $sum;
		}

		$dbf->query("UPDATE users SET reputation = '".str_replace(",",".",$total)."' WHERE id = $uid");

		include_once("funcs/Cache.php");
		$ch = &new Cache();
		$ch->clear_by_id("http://balancer.ru/user/$uid/avatarblock/");

		include_once("funcs/navigation/go.php");
		go("http://balancer.ru/user/$uid/reputation/");
	}
