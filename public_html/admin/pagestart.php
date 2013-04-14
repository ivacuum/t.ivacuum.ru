<?php

define('IN_ADMIN', true);

require '../common.php';
require SITE_DIR . 'attach_mod/attachment_mod.php';
require SITE_DIR . 'attach_mod/includes/functions_admin.php';
require_once SITE_DIR . 'includes/functions_admin.php';

$user->session_start();

if (IS_GUEST)
{
	redirect("login.php?redirect=admin/index.php");
}
if (!IS_ADMIN)
{
	message_die(GENERAL_MESSAGE, $lang['Not_admin']);
}
if (!$userdata['session_admin'])
{
  $redirect = url_arg($_SERVER['REQUEST_URI'], 'admin', 1);
  redirect("login.php?redirect=$redirect");
}
