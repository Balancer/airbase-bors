<?php
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

	include_once("config/mail.php");
