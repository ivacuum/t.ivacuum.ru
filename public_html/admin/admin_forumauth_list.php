<?php

// ACP Header - START
if (!empty($setmodules))
{
	$module['Forums']['Permissions_List'] = basename(__FILE__);
	return;
}
require('./pagestart.php');
// ACP Header - END


//                            View       Read       Post       Reply      Edit      Delete     Sticky    Announce     Vote       Poll    PostAttach  Download
$simple_auth_ary = array(
/* Public */     0 => array(AUTH_ALL,  AUTH_ALL,  AUTH_ALL,  AUTH_ALL,  AUTH_REG,  AUTH_REG,  AUTH_MOD,  AUTH_MOD,  AUTH_REG,  AUTH_REG,  AUTH_REG,  AUTH_ALL), // Public
/* Reg */        1 => array(AUTH_ALL,  AUTH_ALL,  AUTH_REG,  AUTH_REG,  AUTH_REG,  AUTH_REG,  AUTH_MOD,  AUTH_MOD,  AUTH_REG,  AUTH_REG,  AUTH_REG,  AUTH_REG), // Registered
/* Reg [Hid] */  2 => array(AUTH_REG,  AUTH_REG,  AUTH_REG,  AUTH_REG,  AUTH_REG,  AUTH_REG,  AUTH_MOD,  AUTH_MOD,  AUTH_REG,  AUTH_REG,  AUTH_REG,  AUTH_REG), // Registered [Hidden]
/* Priv */       3 => array(AUTH_REG,  AUTH_ACL,  AUTH_ACL,  AUTH_ACL,  AUTH_ACL,  AUTH_ACL,  AUTH_MOD,  AUTH_MOD,  AUTH_ACL,  AUTH_ACL,  AUTH_ACL,  AUTH_ACL), // Private
/* Priv [Hid] */ 4 => array(AUTH_ACL,  AUTH_ACL,  AUTH_ACL,  AUTH_ACL,  AUTH_ACL,  AUTH_ACL,  AUTH_MOD,  AUTH_MOD,  AUTH_ACL,  AUTH_ACL,  AUTH_ACL,  AUTH_ACL), // Private [Hidden]
/* MOD */        5 => array(AUTH_REG,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD), // Moderators
/* MOD [Hid] */  6 => array(AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD,  AUTH_MOD), // Moderators [Hidden]
);

$simple_auth_types = array(
	$lang['Public'],
	$lang['Registered'],
	$lang['Registered'] .' ['. $lang['Hidden'] .']',
	$lang['Private'],
	$lang['Private'] .' ['. $lang['Hidden'] .']',
	$lang['MODERATORS'],
	$lang['MODERATORS'] .' ['. $lang['Hidden'] .']',
);

$forum_auth_fields = array(
	'auth_view',
	'auth_read',
	'auth_reply',
	'auth_edit',
	'auth_delete',
	'auth_vote',
	'auth_pollcreate',
	'auth_attachments',
	'auth_download',
	'auth_post',
	'auth_sticky',
	'auth_announce',
);

$field_names = array();
foreach ($forum_auth_fields as $auth_type)
{
	$field_names[$auth_type] = $lang[$auth_type];
}

$forum_auth_levels = array('ALL',   'REG',    'PRIVATE', 'MOD',    'ADMIN');
$forum_auth_const  = array(AUTH_ALL, AUTH_REG, AUTH_ACL,  AUTH_MOD, AUTH_ADMIN);

if(isset($_GET[POST_FORUM_URL]) || isset($_POST[POST_FORUM_URL]))
{
	$forum_id = (isset($_POST[POST_FORUM_URL])) ? intval($_POST[POST_FORUM_URL]) : intval($_GET[POST_FORUM_URL]);
	$forum_sql = "AND forum_id = $forum_id";
}
else
{
	unset($forum_id);
	$forum_sql = '';
}

if(isset($_GET[POST_CAT_URL]) || isset($_POST[POST_CAT_URL]))
{
	$cat_id = (isset($_POST[POST_CAT_URL])) ? intval($_POST[POST_CAT_URL]) : intval($_GET[POST_CAT_URL]);
	$cat_sql = "AND c.cat_id = $cat_id";
}
else
{
	unset($cat_id);
	$cat_sql = '';
}

if( isset($_GET['adv']) )
{
	$adv = intval($_GET['adv']);
}
else
{
	unset($adv);
}

//
// Start program proper
//
if( isset($_POST['submit']) )
{
	$sql = '';

	if(!empty($forum_id))
	{
		if(isset($_POST['simpleauth']))
		{
			$simple_ary = $simple_auth_ary[intval($_POST['simpleauth'])];

			for($i = 0; $i < count($simple_ary); $i++)
			{
				$sql .= ( ( $sql != '' ) ? ', ' : '' ) . $forum_auth_fields[$i] . ' = ' . $simple_ary[$i];
			}

			if (is_array($simple_ary))
			{
				$sql = "UPDATE " . FORUMS_TABLE . " SET $sql WHERE forum_id = $forum_id";
			}
		}
		else
		{
			for($i = 0; $i < count($forum_auth_fields); $i++)
			{
				$value = intval($_POST[$forum_auth_fields[$i]]);

				if ( $forum_auth_fields[$i] == 'auth_vote' )
				{
					if ( $_POST['auth_vote'] == AUTH_ALL )
					{
						$value = AUTH_REG;
					}
				}

				$sql .= ( ( $sql != '' ) ? ', ' : '' ) .$forum_auth_fields[$i] . ' = ' . $value;
			}

			$sql = "UPDATE " . FORUMS_TABLE . " SET $sql WHERE forum_id = $forum_id";
		}

		if ( $sql != '' )
		{
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update auth table', '', __LINE__, __FILE__, $sql);
			}
		}

		$forum_sql = '';
		$adv = 0;
	}
	elseif (!empty($cat_id))
	{
		for($i = 0; $i < count($forum_auth_fields); $i++)
		{
			$value = intval($_POST[$forum_auth_fields[$i]]);

			if ( $forum_auth_fields[$i] == 'auth_vote' )
			{
				if ( $_POST['auth_vote'] == AUTH_ALL )
				{
					$value = AUTH_REG;
				}
			}

			$sql .= ( ( $sql != '' ) ? ', ' : '' ) .$forum_auth_fields[$i] . ' = ' . $value;
		}

		$sql = "UPDATE " . FORUMS_TABLE . " SET $sql WHERE cat_id = $cat_id";

		if ( $sql != '' )
		{
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update auth table', '', __LINE__, __FILE__, $sql);
			}
		}

		$cat_sql = '';
	}

	$datastore->update('cat_forums');
	$message = $lang['Forum_auth_updated'] . '<br /><br />' . sprintf($lang['Click_return_forumauth'],  '<a href="' . append_sid("admin_forumauth_list.php") . '">', "</a>");
	message_die(GENERAL_MESSAGE, $message);

} // End of submit

//
// Get required information, either all forums if
// no id was specified or just the requsted forum
// or category if it was
//
$sql = "SELECT f.*
	FROM " . FORUMS_TABLE . " f, " . CATEGORIES_TABLE . " c
	WHERE c.cat_id = f.cat_id
	$forum_sql $cat_sql
	ORDER BY c.cat_order ASC, f.forum_order ASC";
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, "Couldn't obtain forum list", "", __LINE__, __FILE__, $sql);
}

$forum_rows = $db->sql_fetchrowset($result);
$db->sql_freeresult($result);

if( empty($forum_id) && empty($cat_id) )
{
	//
	// Output the summary list if no forum id was
	// specified
	//
	$template->assign_vars(array(
		'TPL_AUTH_FORUM_LIST' => true,

		'L_AUTH_TITLE' => $lang['Permissions_List'],
		'L_AUTH_EXPLAIN' => $lang['Forum_auth_list_explain'],
		'S_COLUMN_SPAN' => count($forum_auth_fields)+1,
	));

	for ($i=0; $i<count($forum_auth_fields); $i++)
	{
		$template->assign_block_vars('forum_auth_titles', array(
			'CELL_TITLE' => $field_names[$forum_auth_fields[$i]])
		);
	}

	// Obtain the category list
	$sql = "SELECT c.cat_id, c.cat_title, c.cat_order
		FROM " . CATEGORIES_TABLE . " c
		ORDER BY c.cat_order";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query categories list', '', __LINE__, __FILE__, $sql);
	}

	$category_rows = $db->sql_fetchrowset($result);
	$cat_count = count($category_rows);

	for ($i=0; $i<$cat_count; $i++)
	{
		$cat_id = $category_rows[$i]['cat_id'];

		$template->assign_block_vars('cat_row', array(
			'CAT_NAME' => htmlCHR($category_rows[$i]['cat_title']),
			'CAT_URL' => append_sid('admin_forumauth_list.php?'.POST_CAT_URL.'='.$category_rows[$i]['cat_id']))
		);

		for ($j=0; $j<count($forum_rows); $j++)
		{
			if ( $cat_id == $forum_rows[$j]['cat_id'] )
			{
				$template->assign_block_vars('cat_row.forum_row', array(
					'ROW_CLASS' => !($j % 2) ? 'row4' : 'row5',
					'FORUM_NAME' => '<a class="'.(($forum_rows[$j]['forum_parent']) ? 'genmed' : 'gen').'" href="'.append_sid('admin_forumauth.php?'.POST_FORUM_URL.'='.$forum_rows[$j]['forum_id']).'">'.htmlCHR($forum_rows[$j]['forum_name']).'</a>',
					'IS_SUBFORUM' => $forum_rows[$j]['forum_parent'],
				));

				for ($k=0; $k<count($forum_auth_fields); $k++)
				{
					$item_auth_value = $forum_rows[$j][$forum_auth_fields[$k]];
					for ($l=0; $l<count($forum_auth_const); $l++)
					{
						if ($item_auth_value == $forum_auth_const[$l])
						{
							$item_auth_level = $forum_auth_levels[$l];
							break;
						}
					}
					$template->assign_block_vars('cat_row.forum_row.forum_auth_data', array(
						'CELL_VALUE' => $lang['Forum_' . $item_auth_level],
						'AUTH_EXPLAIN' => sprintf($lang['Forum_auth_list_explain_' . $forum_auth_fields[$k]], $lang['Forum_auth_list_explain_' . $item_auth_level]))
					);
				}
			}
		}
	}
}
else
{
	//
	// Output the authorisation details if an category id was
	// specified
	//

	//
	// First display the current details for all forums
	// in the category
	//
	for ($i=0; $i<count($forum_auth_fields); $i++)
	{
		$template->assign_block_vars('forum_auth_titles', array(
			'CELL_TITLE' => $field_names[$forum_auth_fields[$i]])
		);
	}

	// Obtain the category list
	$sql = "SELECT c.cat_id, c.cat_title, c.cat_order
		FROM " . CATEGORIES_TABLE . " c
		WHERE c.cat_id = $cat_id
		ORDER BY c.cat_order";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query categories list', '', __LINE__, __FILE__, $sql);
	}

	$category_rows = $db->sql_fetchrowset($result);

	$cat_id = $category_rows[0]['cat_id'];
	$cat_name = $category_rows[0]['cat_title'];

	$template->assign_block_vars('cat_row', array(
		'CAT_NAME' => htmlCHR($cat_name),
		'CAT_URL' => append_sid('admin_forumauth_list.php?'.POST_CAT_URL.'='.$cat_id))
	);

	for ($j=0; $j<count($forum_rows); $j++)
	{
		if ( $cat_id == $forum_rows[$j]['cat_id'] )
		{
			$template->assign_block_vars('cat_row.forum_row', array(
				'ROW_CLASS' => !($j % 2) ? 'row4' : 'row5',
				'FORUM_NAME' => '<a class="'.(($forum_rows[$j]['forum_parent']) ? 'genmed' : 'gen').'" href="'.append_sid('admin_forumauth.php?'.POST_FORUM_URL.'='.$forum_rows[$j]['forum_id']).'">'.htmlCHR($forum_rows[$j]['forum_name']).'</a>',
				'IS_SUBFORUM' => $forum_rows[$j]['forum_parent'],
			));

			for ($k=0; $k<count($forum_auth_fields); $k++)
			{
				$item_auth_value = $forum_rows[$j][$forum_auth_fields[$k]];
				for ($l=0; $l<count($forum_auth_const); $l++)
				{
					if ($item_auth_value == $forum_auth_const[$l])
					{
						$item_auth_level = $forum_auth_levels[$l];
						break;
					}
				}
				$template->assign_block_vars('cat_row.forum_row.forum_auth_data', array(
					'CELL_VALUE' => $lang['Forum_' . $item_auth_level],
					'AUTH_EXPLAIN' => sprintf($lang['Forum_auth_list_explain_' . $forum_auth_fields[$k]], $lang['Forum_auth_list_explain_' . $item_auth_level]))
				);
			}
		}
	}

	//
	// Next generate the information to allow the permissions to be changed
	// Note: We always read from the first forum in the category
	//
	for($j = 0; $j < count($forum_auth_fields); $j++)
	{
		$custom_auth[$j] = '<select name="' . $forum_auth_fields[$j] . '">';

		for($k = 0; $k < count($forum_auth_levels); $k++)
		{
			$selected = ( $forum_rows[0][$forum_auth_fields[$j]] == $forum_auth_const[$k] ) ? ' selected="selected"' : '';
			$custom_auth[$j] .= '<option value="' . $forum_auth_const[$k] . '"' . $selected . '>' . $lang['Forum_' . $forum_auth_levels[$k]] . '</option>';
		}
		$custom_auth[$j] .= '</select>';

		$template->assign_block_vars('forum_auth_data', array(
			'S_AUTH_LEVELS_SELECT' => $custom_auth[$j])
		);
	}

	//
	// Finally pass any remaining items to the template
	//
	$s_hidden_fields = '<input type="hidden" name="' . POST_CAT_URL . '" value="' . $cat_id . '">';

	$template->assign_vars(array(
		'TPL_AUTH_CAT' => true,
		'CAT_NAME' => htmlCHR($cat_name),

		'L_AUTH_TITLE' => $lang['Auth_Control_Category'],
		'L_AUTH_EXPLAIN' => $lang['Cat_auth_list_explain'],

		'S_FORUMAUTH_ACTION' => append_sid("admin_forumauth_list.php"),
		'S_COLUMN_SPAN' => count($forum_auth_fields)+1,
		'S_HIDDEN_FIELDS' => $s_hidden_fields)
	);
}

print_page('admin_forumauth_list.tpl', 'admin');
