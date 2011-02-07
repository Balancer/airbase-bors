<?php

class balancer_retranslate_googlereader extends base_page
{
	function render_engine() { return __CLASS__; }
	function is_auto_url_mapped_class() { return true; }
	function can_be_empty() { return true; }
	function loaded() { return true; }

	function render()
	{
		// ispired from http://www.mezzoblue.com/archives/2008/12/11/authenticati/

		$ch = curl_init('https://www.google.com/accounts/ClientLogin');

		$data = array(
			'accountType' => 'GOOGLE',
			'Email' => config('balabot.login.google'),
			'Passwd' => config('balabot.password.google'),
//			'source'=>'BalaBot retranslator',
			'source' => 'exampleCo-exampleApp-1',
			'service'=>'reader',
		);

		curl_setopt_array($ch, array(
			CURLOPT_TIMEOUT => 10,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS => 3,
			CURLOPT_AUTOREFERER => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $data,
		));

		$loginResult = curl_exec($ch);
		curl_close ($ch);

//		var_dump($loginResult);
//		exit();

		$authRaw = strstr($loginResult, "Auth");
		$authToken = substr($authRaw, 5);
		$user_id = '14299261987225235624';
        $ch = curl_init("http://www.google.com/reader/atom/user/{$user_id}/state/com.google/broadcast");
        $header[] = 'Authorization: GoogleLogin auth='.$authToken;

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $xml = curl_exec($ch);
        curl_close($ch);

		header("Content-Type: text/xml; charset=utf-8");
		return $xml;
	}
}
