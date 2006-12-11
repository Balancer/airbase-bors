<?
	echo module_local_db_mob();

	function module_local_db_mob()
	{
		$mob_id = intval(@$_GET['id']);

		if(!$mob_id)
			return "Не указан NPC";

		$GLOBALS['cms']['cache_disabled'] = false;

		$cache = &new Cache();
		
		if($cache->get('LBR:DB',"mob-$mob_id-2"))
			return $cache->last();
		
		$db = new DataBase('l2jdb', 'la2', 'la2kkk');
		$para = $db->get("SELECT * FROM `npc` WHERE `id`=$mob_id", false);

		if(!$para['name'])
			return "Неизвестный NPC $mob_id";

		$out = "<h2>{$para['name']}</h2>";

		$mob_image = "http://la2.balancer.ru/l2jz/modules/drop/i/mobs/".$db->get("SELECT icon FROM l2jz_mobs WHERE mob_id=".intval($mob_id));
		list($w, $h, $r) = @getimagesize($mob_image);

		$img = "";
		if($w && $h)
			$img = "<img width=\"$w\" height=\"$h\" src=\"$mob_image\" style=\"padding: 10px;\">";
		else
			`echo $mob_id >>  /var/www/la2.balancer.ru/htdocs/cms-local/modules/db/non-images-mobs.txt`;

		$agro = $para['aggro'] ? $para['aggro'] : "нет";
		$faction = $para['faction_id'] && $para['faction_range'] ? $para['faction_range'] : "нет";

		$drop = $db->get_array("SELECT gid, d.itemId as item_id, d.sweep, d.chance, gchance, d.min, d.max, p.name FROM `droplist` d LEFT JOIN prices p ON (d.itemId = p.item_id) WHERE `mobId` = $mob_id ORDER BY d.gid, p.name;");

		if(sizeof($drop)>0)
		{
			$drop_data="<table cellSpacing=\"0\" class=\"btab\" width=\"98%\" style=\"margin: 8px;\"><caption>Данные дропа по группам</caption><tr><td>\n";
			$last_gid = -1;
			foreach($drop as $d)
			{
				if($d['chance'] == 0 || $d['gchance'] == 0)
					continue;

				if($last_gid != $d['gid'])
				{
					$drop_data .= "</td></tr><tr><td>";
					
					$gchance = $d['gchance']/10000.0;
					if($gchance >= 10)
						$gchance = intval($gchance+0.5)."%";
					elseif($gchance > 0)
						$gchance = "1/".intval(100/$gchance);
					else
						$gchance = "0%";
					
					$drop_data .= "<span style=\"font-size: 6pt; color: black;\">Вероятность дропа группы: $gchance</span><br />";
					
					$last_gid = $d['gid'];
				}

				if($d['min']!=$d['max'] || $d['min'] > 1)
					$drop_data .= ($d['min']==$d['max'] ? $d['min'] : $d['min'].'-'.$d['max'])." x ";
				else
					$drop_data .= "&nbsp;";
					
				if($sf = $d['sweep'])
					$drop_data .= "<b>";
				$drop_data .= "<a href=\"http://la2.balancer.ru/db/items/?name={$d['name']}\" style=\"color: orange\" title=\"item_id={$d['item_id']}\">";
				$drop_data .= str_replace(' ','&nbsp;',preg_replace('!^\d+\s*!','',trim($d['name'])));
				$drop_data .= "</a>";
				if($sf) 
					$drop_data .= "</b>";

				$chance = $d['chance']/10000.0;
				if($chance >= 10)
					$chance = intval($chance+0.5)."%";
				elseif($chance > 0)
					$chance = "1/".intval(100/$chance);
				else
					$chance = "0% ({$d['chance']}/{$d['gchance']})";
					
				$drop_data .= "<small>,&nbsp;$chance</small>";
				$drop_data .= "<br />\n";
			}
			$drop_data .= "</td></tr></table>";
		}
		else
			$drop_data="Дропа нет";

		$map = "";

		$MAP_MIN_X = -131072;
		$MAP_MAX_X = 228608;
		$MAP_MIN_Y = -256144;
		$MAP_MAX_Y = 262144;
		
		list($map_img_w, $map_img_h) = getimagesize("{$_SERVER['DOCUMENT_ROOT']}/l2jz/modules/map/i/mediumMap.jpg");
		
		foreach($db->get_array("SELECT * FROM spawnlist WHERE npc_templateid = $mob_id") as $row)
		{
			if(($row['locx']=='0')&&($row['locy']=='0'))
			{
				$loc = $db->get("SELECT loc_x, loc_y FROM locations WHERE loc_id = ".intval($row['loc_id'])." ORDER BY RAND()");
			
				$row['locx'] = $loc['loc_x'];
				$row['locy'] = $loc['loc_y'];
			}
			
			$m_x = intval($map_img_w*($row['locx']-$MAP_MIN_X)/($MAP_MAX_X-$MAP_MIN_X)-3);
			$m_y = intval($map_img_h*($row['locy']-$MAP_MIN_Y)/($MAP_MAX_Y-$MAP_MIN_Y)-3);

			$map .=	"<div style=\"position:absolute;top:{$m_y}px;left:{$m_x}px\"><img src=\"/l2jz/modules/map/i/points/monster.gif\" title=\"{$row['count']}\"></div>";
		}


		$out .=<<<__EOT__
<table class="btab" cellSpacing="0" width="100%">
<tr><td rowSpan="22">$img&nbsp;</td></tr>
<tr><th>Уровень</th><td>{$para['level']}</td></tr>
<tr><th>Тип</th><td>{$para['type']}</td></tr>
<tr><th>Дистанция атаки</th><td>{$para['attackrange']}</td></tr>
<tr><th>Здоровье</th><td>{$para['hp']}</td></tr>
<tr><th>Мана</th><td>{$para['mp']}</td></tr>
<tr><th>Сила, STR</th><td>{$para['str']}</td></tr>
<tr><th>Телосложение, CON</th><td>{$para['con']}</td></tr>
<tr><th>Ловкость, DEX</th><td>{$para['dex']}</td></tr>
<tr><th>Интеллект, INT</th><td>{$para['int']}</td></tr>
<tr><th>Магические способности, WIT</th><td>{$para['wit']}</td></tr>
<tr><th>Опыт (Exp)</th><td>{$para['exp']}</td></tr>
<tr><th>Очки умений (SP)</th><td>{$para['sp']}</td></tr>
<tr><th>Физическая атака</th><td>{$para['patk']}</td></tr>
<tr><th>Физическая защита</th><td>{$para['pdef']}</td></tr>
<tr><th>Скорость физической атаки</th><td>{$para['atkspd']}</td></tr>
<tr><th>Магическая атака</th><td>{$para['matk']}</td></tr>
<tr><th>Магическая защита</th><td>{$para['mdef']}</td></tr>
<tr><th>Скорость магической атаки</th><td>{$para['matkspd']}</td></tr>
<tr><th>Скорость бега</th><td>{$para['runspd']}</td></tr>
<tr><th>Дистанция агрессивности</th><td>$agro</td></tr>
<tr><th>Дистанция помощи</th><td>$faction</td></tr>
</table>

<h3>Места спавна</h3>
<br/>
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" width="461">
  <tr width="{$map_img_w}" height="1"><td colSpan="2" width="{$map_img_w}" height="1"><img src="/l2jz/i/tdot.gif" width="{$map_img_w}" height="1"></td></tr>
  <tr width="{$map_img_w}" height="{$map_img_h}">
	<td valign="top" height="{$map_img_h}" width="1"><img src="/l2jz/i/tdot.gif" width="1" height="{map_img_h}"></td>
    <td valign="top" height="{$map_img_h}" width="{$map_img_w}">
      <div style="position:absolute">
        <img src="http://la2.balancer.ru/l2jz/modules/map/i/bigMap.jpg" width="{$map_img_w}" height="{$map_img_h}" border="1" style="border-color:#000000;" style="position:absolute;top:0px;left:0px;"/>
		$map
      </div>
    </td>
  </tr>
</table>
</div>
<br/><br/>

$drop_data
<ul>
<li>Жирным выделен sweep-дроп.</li>
</ul>
__EOT__;

		$out .= "<h3>Данные спавна</h3><ul>";

		$count = $db->get("SELECT sum(`count`) FROM `spawnlist` WHERE npc_templateid = $mob_id;");
		if($count)
			$out .= "<li>На сервере: $count</li>";
		else
			$out .= "<li>На сервере отсутствует</li>";
		
		$out .= "</ul>";
	
		return $cache->set($out, 86400);
	}
	