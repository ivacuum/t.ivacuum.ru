<?php

define('BB_ROOT', './');
require(BB_ROOT . 'cfg.php');

$req_mode = !empty($_REQUEST['show']) ? (string) $_REQUEST['show'] : 'copyright_holders';

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
	<meta http-equiv="Content-Style-Type" content="text/css" />

	<link rel="stylesheet" href="./templates/default/css/main.css" type="text/css">
</head>
<body>

<style type="text/css">
#infobox-wrap { width: 760px; }
#infobox-body {
	background: #FFFFFF; color: #000000; padding: 1em;
	height: 400px; overflow: auto; border: 1px inset #000000;
}
</style>

<br />
<div id="infobox-wrap" class="bCenter row1">
	<fieldset class="pad_6">
	<legend class="med bold mrg_2 warnColor1">Информация</legend>
		<div class="bCenter">
			<?php include(BB_ROOT . 'misc/html/' . $req_mode . '.html'); ?>
		</div>
		<p class="gen tRight pad_6"><a href="javascript:window.close();" class="gen">[ Закрыть ]</a></p>
	</fieldset>
</div>

</body>
</html>