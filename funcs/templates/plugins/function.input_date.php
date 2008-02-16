<?php

function smarty_function_input_date($params, &$smarty)
{
	include_once("funcs/datetime.php");
	
	extract($params);
		
	$obj = $smarty->get_template_vars('current_form_class');
		
	$date = intval($obj->$name());
	
	if(!$date)
		$date = time();

	$day = strftime('%d', $date);
	$mon = strftime('%m', $date);
	$yea = strftime('%Y', $date);
	$hh = strftime('%H', $date);
	$mm = strftime('%M', $date);
	$ss = strftime('%S', $date);
	
	echo "<select name=\"{$name}_day\">\n";
	for($i = 1; $i<=31; $i++)
		echo "<option value=\"$i\"".($i==$day?' selected="true"':'').">$i</option>\n";
	echo "</select>";

	echo "<select name=\"{$name}_month\">\n";
	for($i = 1; $i<=12; $i++)
		echo "<option value=\"$i\"".($i==$mon?' selected="true"':'').">".month_name_rp($i)."</option>\n";
	echo "</select>";
	
	echo "<select name=\"{$name}_year\">\n";
	for($i = strftime('%Y')+1; $i>strftime('%Y')-20; $i--)
		echo "<option value=\"$i\"".($i==$yea?' selected="true"':'').">$i</option>\n";
	echo "</select>";

	if(!empty($time))
	{
		echo "&nbsp;";
		
		echo "<select name=\"{$name}_hour\">\n";
		for($i = 0; $i<=23; $i++)
			echo "<option value=\"$i\"".($i==$hh?' selected="true"':'').">".sprintf('%02d',$i)."</option>\n";
		echo "</select>";

		echo "<select name=\"{$name}_minute\">\n";
		for($i = 0; $i<=59; $i++)
			echo "<option value=\"$i\"".($i==$mm?' selected="true"':'').">".sprintf('%02d',$i)."</option>\n";
		echo "</select>";
	}
}
