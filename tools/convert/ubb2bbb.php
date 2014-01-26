<?php

/**
	РљРѕРЅРІРµСЂС‚РµСЂ СЃС‚Р°СЂС‹С… UBB-СЃРѕРѕР±С‰РµРЅРёР№.
*/

require_once('../config.php');

main();
bors_exit();

function main()
{
	$base_dir = '/home/backup/agava/var/www/airbase.ru/htdocs/forum';
	foreach(bors_lib_files::find_subdirs($base_dir, 'Forum\d+') as $forum_path)
	{
		$ubb_forum_id = str_replace('Forum', '', $forum_path);
		foreach(bors_lib_files::find_subdirs($base_dir.'/'.$forum_path, '\d{4}') as $forum_sub_path)
		{
			foreach(bors_lib_files::find($base_dir.'/'.$forum_path.'/'.$forum_sub_path, '\d{6}\.cgi') as $file)
			{
				echo $base_dir.'/'.$forum_path.'/'.$forum_sub_path.'/'.$file."\n";
				$ubb_topic_id = preg_replace('/^(\d+)\.cgi$/', '$1', $file);
				$first = true;
				$topic = NULL;
				$topic_id = NULL;
				$posts = array();
				foreach(explode("\n", iconv('cp1251', 'utf-8', file_get_contents($base_dir.'/'.$forum_path.'/'.$forum_sub_path.'/'.$file))) as $s)
				{
					if(!$s)
						continue;

					if($first)
					{
						$first = false;
						// A||||3||Serge Pod||РўР°Рє СЂРµРјРѕРЅС‚РёСЂСѓСЋС‚ Р°РІРёР°РЅРѕСЃС†С‹.||||1||||Serge Pod||00000233
						// A||$Notes||0||$username||$in{topic_subject}||||$in{msg_icon}||||$pubname||$user_number";
						@list($type,$notes,$answers,$username,$subject,$foo,$icon,$foo2,$topic_pubname,$ubb_uid) = explode('||', $s);
						if($type != 'A')
							exit("Unknown type $type\n");

						echo "\t$subject\n";
						$ubb_topic = bors_load('forum_topic_ubb', $ubbt="{$ubb_forum_id}-{$ubb_topic_id}");
						if(!$ubb_topic)
							exit("Can't load ubb topic for {$ubbt}\n");
						$topic = bors_load('balancer_board_topic', $ubb_topic->topic_id());
						if(!$topic)
							exit("Can't load topic {$ubb_topic->topic_id()}\n");
							$topic_id = $topic->id();
					}
					else
					{
						echo '.';
						// Z||000008||MD||04-29-2003||12:58 PM||||Да, вроде как он. Спасибо. <BR> ора :ura:||24.69.255.237||reg||1||MD||00000604||yes||Да, вроде как он. Спас;Конструктор, еще аз сибо.<BR>&nbsp;PS. И ник, пsrc=/forum/smilies/ura.gif alt=":ura:" border=0>
						// Z||000000||$username||$GotTime{HyphenDate}||$GotTime{Time}||$post_email||$message||$ip_number||$reg_status||$in{msg_icon}||$pubname||$user_number||$in{Signature}||".hts_compile($in{message});
						@list($type,$record_id,$post_username,$ubb_date,$ubb_time,$post_email,$message,$post_ip,$reg_status,$icon,$post_pubname,$ubb_user_id,$ubb_user_signature,$ubb_html) = explode('||', $s);

						if($type != 'Z')
							exit("Unknown type $type in $s\n");

						$ubb_date2 = preg_replace('/-99$/','-199-', $ubb_date);
						$ubb_date2 = preg_replace('/-(0\d)$/','-20$1', $ubb_date);
						$ubb_date2 = preg_replace('/^(\d{1,2})-(\d{1,2})-(\d{2,4})$/','$2.$1.$3', $ubb_date2);
						$time = strtotime($ts="$ubb_date2 $ubb_time");
						if(!$time)
							exit("Can't parse time '$ts' ({$ubb_date}) for tid=$topic_id\n");
// var_dump($ts, $time, date('r', $time));
						$posts = bors_find_all('balancer_board_post', array(
							'topic_id' => $topic_id,
							'create_time' => $time,
						));

						if(!$posts)
						{
							$posts = bors_find_all('balancer_board_post', array(
								'topic_id' => $topic_id,
								'create_time' => $time+3600,
							));
						}

						if(!$posts)
						{
							$posts = bors_find_all('balancer_board_post', array(
								'topic_id' => $topic_id,
								'create_time' => $time-3600*3,
							));
						}

						if(!$posts)
						{
							$posts = bors_find_all('balancer_board_post', array(
								'topic_id' => $topic_id,
								'create_time' => $time+3600*9,
							));
						}

						if(!$posts)
							exit("Can't find post ($topic_id, ".date('r', $time)."): $message\n");

//						if(count($posts) > 1)

						$bb_message = preg_replace('/<BR>/i', "\n", $message);
						$bb_message = preg_replace('!<b>(.+?)</b>!i', '[b]$1[/b]', $bb_message);
						$bb_message = preg_replace('!<font color=(\w+)>(.+?)</font>!i', '[$1]$2[/$1]', $bb_message);

						$bb_message = preg_replace('!<p>!i', "\n\n", $bb_message);

						$bb_message_compare = trim(preg_replace('!\[Edited.*?\]!i', "", $bb_message));

						$post = NULL;
						$pss = array();
						$break = false;
						foreach($posts as $p)
						{
							$compare_src = trim(preg_replace('!\[Edited.*?\]!i', "", $p->source()));
							similar_text($bb_message_compare, $compare_src, $similar);
							if($compare_src == $bb_message_compare || $similar > 90 || !$compare_src)
							{
//								var_dump($similar);
								if($post)
								{
									echo "\n#################################################\n";
									foreach($posts as $p)
										echo "{$p->url_in_container()}\n";

//									exit("Multiple posts ($topic_id): ".print_dd($posts));
									$break = true;
									break;
								}

								$post = $p;
							}
							else
							{
								$pss[] = $p->source()."\n~~~~~~~~~~~~~~\n$similar({$p->url_in_container()})";
							}
						}

						if($break)
							continue;

						if(!$post)
							exit("\n\nCan't find post text ($topic_id): ".print_dd($posts)."\n=======\n$bb_message\n------\n".join("\n--------\n", $pss)."\n-----\n\n");

						if(!$post_pubname)
							$post_pubname = $post_username;

						if($post->author_name() != $post_pubname)
						{
							$post->set_author_name($post_pubname);
							echo "\n\tUpdate name ".$post->url_in_container()."\n";
//							exit("pubname: {$post->author_name()} != $post_pubname in ".$post->url_in_container()."\n");
						}

						if($post->create_time() != $time)
						{
							$post->set_create_time($time);
							echo "\n\tUpdate time ".$post->url_in_container()."\n";
						}

						if($post->poster_ip() != $post_ip)
						{
							$post->set_poster_ip($post_ip);
							echo "\n\tUpdate IP ".$post->url_in_container()."\n";
						}
					}
				}
				echo "\n";
			}
		}
	}
}
