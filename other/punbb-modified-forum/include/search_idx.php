<?php
/***********************************************************************

  Copyright (C) 2002-2005  Rickard Andersson (rickard@punbb.org)

  This file is part of PunBB.

  PunBB is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  PunBB is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
  MA  02111-1307  USA

************************************************************************/


// The contents of this file are very much inspired by the file functions_search.php
// from the phpBB Group forum software phpBB2 (http://www.phpbb.com). 


// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;


//
// "Cleans up" a text string and returns an array of unique words
// This function depends on the current locale setting
//
function split_words($text)
{
	global $pun_user;
	static $noise_match, $noise_replace, $stopwords;

	if (empty($noise_match))
	{
		$noise_match = 		array('[quote', '[code', '[url', '[img', '[email', '[color', '[colour', 'quote]', 'code]', 'url]', 'img]', 'email]', 'color]', 'colour]', '^', '$', '&', '(', ')', '<', '>', '`', '\'', '"', '|', ',', '@', '_', '?', '%', '~', '+', '[', ']', '{', '}', ':', '\\', '/', '=', '#', ';', '!', '*');
		$noise_replace =	array('',       '',      '',     '',     '',       '',       '',        '',       '',      '',     '',     '',       '',       '',        ' ', ' ', ' ', ' ', ' ', ' ', ' ', '',  '',   ' ', ' ', ' ', ' ', '',  ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', '' ,  ' ', ' ', ' ', ' ', ' ', ' ');

		$stopwords = (array)@file(PUN_ROOT.'lang/'.$pun_user['language'].'/stopwords.txt');
		$stopwords = array_map('trim', $stopwords);
	} /*'*/

	// Clean up
	$patterns[] = '#&[\#a-z0-9]+?;#i';
	$patterns[] = '#\b[\w]+:\/\/[a-z0-9\.\-]+(\/[a-z0-9\?\.%_\-\+=&\/~]+)?#';
	$patterns[] = '#\[\/?[a-z\*=\+\-]+(\:?[0-9a-z]+)?:[a-z0-9]{10,}(\:[a-z0-9]+)?=?.*?\]#';
	$text = preg_replace($patterns, ' ', ' '.strtolower($text).' ');

	// Filter out junk
	$text = str_replace($noise_match, $noise_replace, $text);

	// Strip out extra whitespace between words
	$text = trim(preg_replace('#\s+#', ' ', $text));

	// Fill an array with all the words
	$words = explode(' ', $text);

	if (!empty($words))
	{
		while (list($i, $word) = @each($words))
		{
			$words[$i] = trim($word, '.');
			$num_chars = pun_strlen($word);

			if ($num_chars < 3 || $num_chars > 20 || in_array($word, $stopwords))
				unset($words[$i]);
		}
	}

	return array_unique($words);
}


//
// Strip search index of indexed words in $post_ids
//
function strip_search_index($post_ids)
{
	return;

	global $db_type, $db;

	switch ($db_type)
	{
		case 'mysql':
		case 'mysqli':
		{
			$result = $db->query('SELECT word_id FROM '.$db->prefix.'search_matches WHERE post_id IN('.$post_ids.') GROUP BY word_id') or error('Unable to fetch search index word match', __FILE__, __LINE__, $db->error());

			if ($db->num_rows($result))
			{
				$word_ids = '';
				while ($row = $db->fetch_row($result))
					$word_ids .= ($word_ids != '') ? ','.$row[0] : $row[0];

				$result = $db->query('SELECT word_id FROM '.$db->prefix.'search_matches WHERE word_id IN('.$word_ids.') GROUP BY word_id HAVING COUNT(word_id)=1') or error('Unable to fetch search index word match', __FILE__, __LINE__, $db->error());

				if ($db->num_rows($result))
				{
					$word_ids = '';
					while ($row = $db->fetch_row($result))
						$word_ids .= ($word_ids != '') ? ','.$row[0] : $row[0];

					$db->query('DELETE FROM '.$db->prefix.'search_words WHERE id IN('.$word_ids.')') or error('Unable to delete search index word', __FILE__, __LINE__, $db->error());
				}
			}

			break;
		}

		default:
			$db->query('DELETE FROM '.$db->prefix.'search_words WHERE id IN(SELECT word_id FROM '.$db->prefix.'search_matches WHERE word_id IN(SELECT word_id FROM '.$db->prefix.'search_matches WHERE post_id IN('.$post_ids.') GROUP BY word_id) GROUP BY word_id HAVING COUNT(word_id)=1)') or error('Unable to delete from search index', __FILE__, __LINE__, $db->error());
			break;
	}

	$db->query('DELETE FROM '.$db->prefix.'search_matches WHERE post_id IN('.$post_ids.')') or error('Unable to delete search index word match', __FILE__, __LINE__, $db->error());
}
