<?php

if (!defined('SITE_DIR'))
{
	exit;
}

global $db, $bb_cfg, $static_path;

$smilies = array();

$rowset = $db->fetch_rowset("SELECT * FROM ". SMILIES_TABLE);
usort($rowset, 'smiley_sort');

foreach ($rowset as $smile)
{
	$smilies['orig'][] = '#(?<=^|\W)'. preg_quote($smile['code'], '#') .'(?=$|\W)#';
	$smilies['repl'][] = ' <img class="smile" src="' . $static_path . '/i/tracker/smilies/' . $smile['smile_url'] .'" alt="'. $smile['emoticon'] .'" border="0">';
}

$this->store('smile_replacements', $smilies);
