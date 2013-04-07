<?php

if (!defined('SITE_DIR'))
{
	exit;
}

// Lock tables
$db->lock(array(
	'bb_topics t',
	'buf_topic_view buf',
));

// Flash buffered records
$db->query("
	UPDATE
		bb_topics t,
		buf_topic_view buf
	SET
		t.topic_views = t.topic_views + buf.topic_views
	WHERE
		t.topic_id = buf.topic_id
");

// Delete buffered records
$db->query("DELETE buf FROM buf_topic_view buf");

// Unlock tables
$db->unlock();

