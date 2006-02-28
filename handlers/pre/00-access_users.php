<?
    register_uri_handler('!/users/((makeorder)/)?$!', 'handler_check_users_access');

    function handler_check_users_access($uri, $m=array())
	{
		if(!user_data('id'))
		{
			require_once('funcs/modules/messages.php');

			$s = <<<__EOT__
<h2>Авторизуйтесь, пожалуйста:</h2>
<form action="/users/?do-login" method="post">
<table>
<tr><td align="right">Логин:</td><td><input name="login"/></td></tr>
<tr><td align="right">Пароль:</td><td><input name="password" type="password"/></td></tr>
<tr><td></td><td><input type="submit" value="Зайти"/></td></tr>
</table>
</form>
<br/>
<ul>
<li><a href="/users/register/">Регистрация нового пользователя</a></li>
</ul>
__EOT__;
			message(ec($s), false);

			return true;
		}

		return false;
    }
?>
