<?
    function send_mail($from, $to, $subject, $text)
    {
		include_once('Mail.php');

//		$recipients = 'joe@example.com';

		$headers['From']    = $from;
		$headers['To']      = $to;
		$headers['Subject'] = encode_mimeheader($subject, $GLOBALS['cms']['charset']);
		$headers['Content-Type'] = "text/plain; charset=\"{$GLOBALS['cms']['charset']}\"";

		$params['sendmail_path'] = '/usr/lib/sendmail';

//		$params['host'] = @$GLOBALS['cms']['smtp_host'];
//		if(!$params['host'])
//			$params['host'] = '127.0.0.1';

		// Create the mail object using the Mail::factory method
		$mail_object =& Mail::factory('sendmail', $params);
//		$mail_object =& Mail::factory('smtp', $params);
//		$mail_object =& Mail::factory('mail', $params);

		$mail_object->send($to, $headers, $text);
		
//		exit();
    }

	function encode_mimeheader($string, $charset=null, $linefeed="\n") 
	{
		if (!$charset)
        	$charset = mb_internal_encoding();
		  
		$start = "=?$charset?B?";
		$end = "?=";
		$encoded = '';
				   
		/* Each line must have length <= 75, including $start and $end */
		$length = 75 - strlen($start) - strlen($end);
		/* Average multi-byte ratio */
		$ratio = strlen($string) / strlen($string);
		/* Base64 has a 4:3 ratio */
		$magic = $avglength = floor(3 * $length * $ratio / 4);
								 
		for ($i=0; $i <= strlen($string); $i+=$magic) 
		{
			$magic = $avglength;
			$offset = 0;
			/* Recalculate magic for each line to be 100% sure */
			do 
			{
				$magic -= $offset;
				$chunk = substr($string, $i, $magic);
				$chunk = base64_encode($chunk);
				$offset++;
			} while (strlen($chunk) > $length);
			if ($chunk)
				$encoded .= ' '.$start.$chunk.$end.$linefeed;
		}
		/* Chomp the first space and the last linefeed */
		$encoded = substr($encoded, 1, -strlen($linefeed));
	
		return $encoded;
	}
?>
