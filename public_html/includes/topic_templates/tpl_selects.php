<?php

if (!defined('SITE_DIR'))
{
	exit;
}

$selects = array(
	'SEL_VIDEO_QUALITY' => array(
		'DVDRip',
		'DVDRip-AVC',
		'DVD5',
		"DVD5 {$lang['tpl']['compressed']}",
		'DVD9',
		'Blu-Ray',
		'BDRip',
		'HD-DVDRip',
		'HDTV',
		'HDTVRip',
		'HDRip',
		'IPTVRip',
		'TVRip',
		'TeleCine',
		'TeleSynch',
		'CamRip',
		'SATRip',
		'VHSRip',
		'DVDScreener',
	),

	'SEL_VIDEO_CODECS' => array(
		'DivX',
		'XviD',
		"{$lang['bt_other']} MPEG4",
		'VPx',
		'MPEG1',
		'MPEG2',
		'Windows Media',
		'QuickTime',
		'H.264',
		'Flash',
	),

	'SEL_VIDEO_FORMATS' => array(
		'AVI',
		'DVD Video',
		'OGM',
		'MKV',
		'WMV',
		'MPEG',
		'MP4',
		'TS',
		'M2TS',
	),

	'SEL_AUDIO_CODECS' => array(
		'AAC',
		'AC3',
		'ALAC (image + .cue)',
		'ALAC (tracks)',
		'APE (image + .cue)',
		'APE (tracks)',
		'DTS',
		'DVD-Audio',
		'FLAC (image + .cue)',
		'FLAC (tracks)',
		'M4A (image + .cue)',
		'M4A (tracks)',
		'M4B',
		'MP3',
		'MPEG Audio',
		'OGG Vorbis',
		'SHN (image + .cue)',
		'SHN (tracks)',
		'TTA (image + .cue)',
		'TTA (tracks)',
		'WAVPack (image + .cue)',
		'WAVPack (tracks)',
		'WMA',
	),

	'SEL_BITRATE' => array(
		'lossless',
		'64 kbps',
		'128 kbps',
		'160 kbps',
		'192 kbps',
		'224 kbps',
		'256 kbps',
		'320 kbps',
		'VBR 128-192 kbps',
		'VBR 192-320 kbps',
	),

	'SEL_TEXT_FORMATS' => array(
		$lang['tpl']['simple_text'],
		'PDF',
		'DjVu',
		'CHM',
		'HTML',
		'DOC',
	),

	'SEL_TEXT_QUALITY' => array(
		$lang['tpl']['scanned'],
		$lang['tpl']['native'],
		$lang['tpl']['ocr_w_o_errors'],
		$lang['tpl']['ocr_w_errors'],
	),

	'SEL_SOURCE_TYPE' => $lang['tpl']['source_type_options'],

	'SEL_LOCALIZATION' => array(
		$lang['tpl']['not_needed'],
		$lang['tpl']['included'],
		$lang['tpl']['not_included'],
	),

	'SEL_LANG' => $lang['tpl']['lang_options'],

	'SEL_UI_LANG' => $lang['tpl']['ui_lang_options'],

	'SEL_UI_LANG_PS' => $lang['tpl']['ui_lang_options_ps'],

	'SEL_AUDIOBOOK_TYPE' => $lang['tpl']['audiobook_type_options'],

	'SEL_MEDICINE' => array(
		$lang['tpl']['not_needed'],
		$lang['tpl']['included'],
		$lang['tpl']['not_included'],
	),

	'SEL_VISTA_COMPATIBLE' => $lang['tpl']['vista_compatible_options'],

	'SEL_TRANSLATION' => $lang['tpl']['translation_options'],

	'SEL_TRANSLATION_TYPE' => $lang['tpl']['translation_types'],

	'SEL_PLATFORM_PS' => array('PS', 'PS2'),

	'SEL_MULTIPLAYER' => $lang['tpl']['multiplayer_options'],

	'SEL_REGION' => array('PAL', 'NTSC'),
);

foreach ($selects as $tpl_name => $sel_ary)
{
	$template->assign_vars(array(
		$tpl_name => join("','", replace_quote($sel_ary))
	));
}

