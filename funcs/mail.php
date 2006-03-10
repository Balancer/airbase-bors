<?
    function send_mail($from, $to, $subject, $text)
    {
		include_once('Mail.php');

		$recipients = 'joe@example.com';

		$headers['From']    = $from;
		$headers['To']      = $to;
		$headers['Subject'] = $subject;
		$headers['Content-Type'] = "text/plain; charset=\"{$GLOBALS['cms']['charset']}\"";

//		$params['sendmail_path'] = '/usr/lib/sendmail';

		$params['host'] = '192.168.1.1';

		// Create the mail object using the Mail::factory method
//		$mail_object =& Mail::factory('sendmail', $params);
		$mail_object =& Mail::factory('smtp', $params);

		$mail_object->send($to, $headers, $text);
    }
?>
