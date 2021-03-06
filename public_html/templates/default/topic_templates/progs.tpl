<script type="text/javascript">
var localization = ['{SEL_UI_LANG}'];
var medicine = ['{SEL_MEDICINE}'];
var sel_vista_compatible = ['{SEL_VISTA_COMPATIBLE}'];
var torrent_sign = "{TORRENT_SIGN}";

function make_format_list (what)
{
	var ret='';
	for (i=0; i<what.length; i++)
	{
		ret += '<option value="'+what[i]+'">'+what[i]+'</option>';
	}
	return ret;
}
function form_validate (f)
{
	var error='';
	var msg="\n\n";

	if (f.elements["msg[release_name]"].value=='')
	{
		f.elements["msg[release_name]"].focus();
		error='{L_TITLE}';
		msg +='{L_TITLE_EXP}';
	}
	else if (f.elements["msg[picture]"].value!='' && !f.elements["msg[picture]"].value.match('^(http|https)://[^ \?&=\#\"<>]+?\.(jpg|jpeg|gif|png)$'))
	{
		f.elements["msg[picture]"].focus();
		error='{L_PICTURE}';
		msg +='{L_PICTURE_EXP}';
	}
	else if (f.elements["msg[year]"].value!='' && (isNaN(f.elements["msg[year]"].value) || f.elements["msg[year]"].value.length!=4))
	{
		f.elements["msg[year]"].focus();
		error='{L_YEAR}';
		msg +='{L_YEAR_EXP}';
	}
	/*
	else if (f.fileupload.value=='')
	{
		f.fileupload.focus();
		error='{L_TORRENT}';
		msg +='{L_TORRENT_EXP}';
	}
	else if (f.fileupload.value.substr(f.fileupload.value.length-{TORRENT_EXT_LEN})!='.{TORRENT_EXT}')
	{
		f.fileupload.focus();
		error='{L_TORRENT}';
		msg +='{L_TORRENT_EXP}';
	}
	*/
	else if (torrent_sign && f.fileupload.value.indexOf(torrent_sign) == -1)
	{
		f.fileupload.focus();
		error='{L_TORRENT}';
		msg +='{L_TORRENT_SIGN_EXP}';
	}

	if (error) {
		alert('{L_ERROR}: '+error+msg);
		return false;
	}
	return true;
}
</script>

<h1 class="maintitle"><a href="{U_VIEW_FORUM}">{FORUM_NAME}</a></h1>

<div class="nav">
	<p class="floatL"><a href="{U_INDEX}">{T_INDEX}</a></p>
	<!-- IF REGULAR_TOPIC_BUTTON --><p class="floatR"><a href="{REGULAR_TOPIC_HREF}">{L_POST_REGULAR_TOPIC}</a></p><!-- ENDIF -->
	<div class="clear"></div>
</div>

<?php require $GLOBALS['bb_cfg']['topic_tpl']['overall_header']; ?>

<form action="{S_ACTION}" method="post" name="post" onsubmit="return form_validate(this);" enctype="multipart/form-data">
<input type="hidden" name="preview" value="1">

<table class="forumline">
<col class="row1" width="20%">
<col class="row2" width="80%">
<tr>
	<th colspan="2">{L_RELEASE_WELCOME}</th>
</tr>
<tr>
	<td><b>{L_TITLE}</b>:</td>
	<td><input type="text" name="msg[release_name]" maxlength="90" size="80" /></td>
</tr>
<tr>
	<td><b>{L_PICTURE}</b>:</td>
	<td><input type="text" name="msg[picture]" size="80" /> <span class="med">URL</span> <a class="med" href="http://up.local.ivacuum.ru/" target="_blank"><b>Загрузить картинку</b></a></td>
</tr>
<tr>
	<td><b>{L_YEAR}</b>:</td>
	<td><input type="text" name="msg[year]" maxlength="4" size="5" /></td>
</tr>
<tr>
	<td><b>{L_VERSION}</b>:</td>
	<td>
		<input type="text" name="msg[version]" size="30" />&nbsp;
		<select name="msg[vista_compatible]"><option value="">&raquo; {L_VISTA_COMPATIBLE} &nbsp;</option><script type="text/javascript">document.writeln(make_format_list(sel_vista_compatible));</script></select>&nbsp;
	</td>
</tr>
<tr>
	<td><b>{L_DEVELOPER}</b>:</td>
	<td><input type="text" name="msg[developer][name]" size="60" /></td>
</tr>
<tr>
	<td><b>{L_DEVELOPER_URL}</b>:</td>
	<td><input type="text" name="msg[developer][url]" size="60" /> <span class="med">URL</span></td>
</tr>
<tr>
	<td><b>{L_PLATFORM}</b>:</td>
	<td>
		<input type="text" name="msg[platform]" size="40" />
		<select name="msg[localization]"><option value="">&raquo; {L_LOCALIZATION}</option><script type="text/javascript">document.writeln(make_format_list(localization));</script></select>&nbsp;
		<select name="msg[medicine]"><option value="">&raquo; {L_MEDICINE}</option><script type="text/javascript">document.writeln(make_format_list(medicine));</script></select>&nbsp;
	</td>
</tr>
<tr>
	<td><b>{L_SYS_REQUIREMENTS}</b>:</td>
	<td><textarea name="msg[sys_requirements]" rows="3" cols="100" class="editor"></textarea></td>
</tr>
<tr>
	<td><b>{L_DESCRIPTION}</b>:</td>
	<td><textarea name="msg[description]" rows="10" cols="100" class="editor"></textarea></td>
</tr>
<tr>
	<td><b>{L_MOREINFO}</b>:</td>
	<td><textarea name="msg[moreinfo]" rows="3" cols="100" class="editor"></textarea></td>
</tr>
<tr>
	<td><b>{L_SCREEN_SHOTS}</b>:</td>
	<td><textarea name="msg[screen_shots]" rows="3" cols="100" class="editor"></textarea> <span class="med">URLs</span></td>
</tr>
<tr>
	<td><b>{L_TORRENT}</b>:</td>
	<td>
		<p><input type="file" name="fileupload" size="65" /></p>
		<p class="med">{L_TORRENT_EXP}</p>
	</td>
</tr>
<tr>
	<td class="catBottom" colspan="2">
		<input type="submit" name="add_attachment" value="{L_NEXT}" class="bold" />
	</td>
</tr>
</table>

</form>
