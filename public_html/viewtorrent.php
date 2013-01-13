<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

define('IN_PHPBB', TRUE);
define('IN_AJAX', TRUE);
$t_root_path = __DIR__ . '/';
require($t_root_path . 'common.php');
require($t_root_path . 'attach_mod/attachment_mod.php');
require($t_root_path . 'includes/functions_torrent.php');

$attach_id = request_var('t', 0);

$torrent = get_torrent_info($attach_id);

$filename = $phpbb_root_path . $attach_config['upload_dir'] . '/' . $torrent['physical_filename'];

$tor = bdecode_file($filename);

$info = $tor['info'];

if( isset($info['length']) && $info['length'] )
{
	printf('%s <i>%d</i>', htmlspecialchars($info['name']), $info['length']);
	exit;
}

$html = '<div class="tor-root-dir">' . htmlspecialchars($info['name']) . '</div><ul class="tree-root"><li><span>' . (( sizeof($info['files']) > 1000 ) ? '[ ... cut: 1000 files max ... ]' : '') . '</span></li>';

if( isset($info['files']) && is_array($info['files']) )
{
	$can_create_folder = 1;
	$files_in_folder = 0;
	$folder_created  = 0;
	$folders_history = array();
	$opened_level    = 0;
	$previous_folder = '';
	$previous_level  = 0;
	$previous_path   = array();

	foreach( $info['files'] as $n => $f )
	{
		if( $n > 1000 )
		{
			break;
		}

		$current_path   = array_slice($f['path'], 0, -1);
		$current_folder = join('/', $current_path);
		$current_level  = sizeof($f['path']) - 1;

		if( $current_folder != $previous_folder && $files_in_folder )
		{
			/* Мы перешли в другую папку, а в прошлой были файлы */
			if( $previous_level > 0 )
			{
				$previous_level--;
				$opened_level--;
				$can_create_folder = 0;
			}

			$files_in_folder = 0;
			$html .= '</ul></li>';
		}

		$diff = array_diff_assoc($current_path, $previous_path);

		if( !empty($diff) )
		{
			for( $i = $previous_level, $len = key($diff); $i > $len; $i-- )
			{
				$html .= ( $i > 0 ) ? '</ul></li>' : '</li>';
			}

			$previous_level = key($diff);
			$opened_level = key($diff) + 1;
		}

		if( $previous_level == 0 )
		{
			$opened_level = 1;
		}

		if( $current_level > $previous_level )
		{
			/**
			* Подстраховка при переходе, например, с 3-го уровня на 5-й минуя первые (учитывая, что они другие)
			*
			* 4. Windows/Crack/New/file.exe
			* 3. Windows/Crack/setup.exe
			* 5. Mac/Dmg/New/10.6/file.dmg
			*/
			for( $i = $previous_level; $i < $current_level; $i++ )
			{
				$html .= (( $i + 1 > $opened_level ) ? '<ul>' : '') . '<li><span class="b">' . htmlspecialchars($f['path'][$i]) . '</span>';
			}

			$folder_created = 1;
		}

		if( $current_level < $previous_level )
		{
			/* Следующая папка ниже по уровню */
			for( $i = $previous_level; $i > $current_level; $i-- )
			{
				$html .= ( $i > 1 ) ? '</ul></li>' : '</li>';
			}
		}

		/* Файл */
		$html .= (( !$files_in_folder && $folder_created ) ? '<ul>' : '') . '<li><span>' . htmlspecialchars($f['path'][$current_level]) . ' <i>' . $f['length'] . '</i></span></li>';

		$can_create_folder = 1;
		$files_in_folder++;
		$folder_created  = 0;
		$opened_level    = $current_level + 1;
		$previous_folder = $current_folder;
		$previous_level  = $current_level;
		$previous_path   = $current_path;
	}

	for( $i = $current_level; $i > 0; $i-- )
	{
		$html .= ( $i != 0 ) ? '</ul></li>' : '</ul>';
	}
}

print $html;

?>