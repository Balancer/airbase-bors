<?php

global $GEOIP_REGION_NAME;

require_once(BORS_3RD_PARTY.'/geoip/geoip.inc');
require_once(BORS_3RD_PARTY.'/geoip/geoipcity.inc');
require_once(BORS_3RD_PARTY.'/geoip/geoipregionvars.php');

function get_flag($ip, $owner = NULL)
{
	if(!$ip)
		return "";

	global $GEOIP_REGION_NAME;
		
	$ch = &new Cache();
	if($ch->get("country_flag-v14", $ip))
		return $ch->last();


	$gi = geoip_open(BORS_3RD_PARTY.'/geoip/GeoLiteCity.dat', GEOIP_STANDARD);

	$record = geoip_record_by_addr($gi, $ip);
	$country_code = $record->country_code;
	$country_name = $record->country_name;
	$city_name = $record->city;
	$region_code = $record->region;
	geoip_close($gi);
		
	if(!$country_code)
	{
		$gi = geoip_open(BORS_3RD_PARTY.'/geoip/GeoIP.dat', GEOIP_STANDARD);
		$country_code = geoip_country_code_by_addr($gi, $ip);
		$country_name = geoip_country_name_by_addr($gi, $ip);
		$region_code = geoip_region_by_addr($gi, $ip);
		$city_name = "";
		geoip_close($gi);
	}

	$region_name = @$GEOIP_REGION_NAME[$country_code][$region_code];

	if($country_code)
	{
		$alt = array();
		
		if($owner && $owner->id() == 10000) //Fun :)
			$alt[] = 'Earth';
		
		if($country_name)
			$alt[] = $country_name;
		if($region_name && $region_name != $city_name && $region_name != $city_name.' City')
			$alt[] = $region_name.(preg_match('!of$!', $region_name)?'':' region');
		if($city_name)
			$alt[] = $city_name;

//		$alt[] = "cc=$country_code, rc=$region_code, $region_name";

		$alt = join(', ', $alt);

		$file = strtolower($country_code).".gif";
		if(!file_exists("/var/www/balancer.ru/htdocs/img/flags/$file"))
			$file = "-.gif";
		$res = '<img src="http://balancer.ru/img/flags/'.$file.'" class="flag" title="'.htmlspecialchars($alt).'" alt="'.$country_code.'"/>';
	}
	else
		$res = "";

	return $ch->set($res, -3600);
}

function get_my_flag()
{
	$outher = get_flag($_SERVER['REMOTE_ADDR']);
	if(!$_SERVER['HTTP_X_FORWARDED_FOR'])
		return $outher;
			
	$inner = get_flag($_SERVER['HTTP_X_FORWARDED_FOR']);
	if($inner && $inner != $outher)
		return "$outher/$inner";
	else
		return $outher;
}

function bors_geo_place_title($ip)
{
	if(!$ip)
		return "";

	global $GEOIP_REGION_NAME;

	$ch = &new Cache();
	if($ch->get("country_flag-v14", $ip))
		return $ch->last();

	if(!file_exists(BORS_3RD_PARTY.'/geoip/GeoLiteCity.dat'))
		return "";

	$gi = geoip_open(BORS_3RD_PARTY.'/geoip/GeoLiteCity.dat', GEOIP_STANDARD);

	$record = geoip_record_by_addr($gi, $ip);
	$country_code = $record->country_code;
	$country_name = $record->country_name;
	$city_name = $record->city;
	$region_code = $record->region;
	geoip_close($gi);

	if(!$country_code)
	{
		$gi = geoip_open(BORS_3RD_PARTY.'/geoip/GeoIP.dat', GEOIP_STANDARD);
		$country_code = geoip_country_code_by_addr($gi, $ip);
		$country_name = geoip_country_name_by_addr($gi, $ip);
		$region_code = geoip_region_by_addr($gi, $ip);
		$city_name = "";
		geoip_close($gi);
	}

	$region_name = @$GEOIP_REGION_NAME[$country_code][$region_code];

	if($country_code)
	{
		$alt = array();
		
		if($country_name)
			$alt[] = $country_name;
		if($region_name && $region_name != $city_name && $region_name != $city_name.' City')
			$alt[] = $region_name.(preg_match('!of$!', $region_name)?'':' region');
		if($city_name)
			$alt[] = $city_name;

//		$alt[] = "cc=$country_code, rc=$region_code, $region_name";

		$res = join(', ', $alt);
	}
	else
		$res = "";

	return $ch->set($res, -3600);
}
