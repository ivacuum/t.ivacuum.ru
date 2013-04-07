<?php

if (!defined('SITE_DIR'))
{
	exit;
}

global $db;

// Don't count on forbidden extensions table, because it is not allowed to allow forbidden extensions at all
$extensions = $db->fetch_rowset("
	SELECT
	  e.extension, g.cat_id, g.download_mode, g.upload_icon
	FROM
	  bb_extensions e,
	  bb_extension_groups g
	WHERE
	      e.group_id = g.group_id
	  AND g.allow_group = 1
");

$this->store('attach_extensions', $extensions);
