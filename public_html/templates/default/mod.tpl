<span class="maintitle">{L_MODERATE_PANEL}</span><br />
<span class="gensmall">{L_MODERATE_PANEL_EXPLAIN}</span><br /><br />

<!-- BEGIN tor_topics -->
<script type="text/javascript">
function checkBox(field)
{
	checkBoxes = $('.topic-chbox');
	checkButton = field.form["checkButton"];

	for (i = 0; i < checkBoxes.length; i++)
	{
		checkBoxes[i].checked = (checkButton.value == "{L_CHECK_ALL}") ? true : false;
	}
	return (checkButton.value == "{L_CHECK_ALL}") ? "{L_UNCHECK_ALL}" : "{L_CHECK_ALL}";
}

function checkBoxInverse(field)
{
	checkBoxes = field.form["topic_id[]"];

	for (i = 0; i < checkBoxes.length; i++)
	{
		checkBoxes[i].checked = (checkBoxes[i].checked) ? false : true;
	}
}

ajax.openedPosts = {};
ajax.view_post = function(post_id, src) {
	if (!ajax.openedPosts[post_id]) {
		ajax.exec({
			action  : 'view_post',
			post_id : post_id
		});
	}
	else {
		var $post = $('#post_'+post_id);
		if ($post.is(':visible')) {
			$post.hide();
		} else {
			$post.css({ display: '' });
		}
	}
	$(src).toggleClass('unfolded2');
};

ajax.callback.view_post = function(data) {
	var post_id = data.post_id;
	var $tor = $('#tor_'+post_id);
	window.location.href='#tor_'+post_id;
	$('#post-row tr')
		.clone()
		.attr({ id: 'post_'+post_id })
		.find('div.post_body').html(data.post_html).end()
		.insertAfter($tor)
	;
	initPostBBCode('#post_'+post_id);
	var maxH   = screen.height - 290;
	var maxW   = screen.width - 60;
	var $post  = $('div.post_wrap', $('#post_'+post_id));
	$post.css({ maxHeight: maxH });
	if ($.browser.msie) {
		if ($post.height() > maxH) { $post.height(maxH); }
		if ($post.width() > maxW)  { $post.width(maxW); $links.width(maxW); }
	}
	ajax.openedPosts[post_id] = true;
};

$(document).ready(function() {
	$('input.topic-chbox').click(function() { $('#tor_' + $(this).attr('rel')).toggleClass('hl-selected-topic'); });
});
</script>
<style type="text/css">
.post_wrap { border: 1px #A5AFB4 solid; margin: 8px 8px 6px; overflow: auto; }
</style>

<table width="100%" id="post-row" style="display: none;">
<tr>
	<td class="row2" colspan="9">
		<div class="post_wrap row1">
			<div class="post_body pad_6 w10"></div>
			<div class="clear"></div>
		</div>
	</td>
</tr>
</table>
<!-- END tor_topics -->

<table width="100%">
<tr>
	<td class="nav">
		<span class="nav">
			<a href="{U_INDEX}" class="nav">{T_INDEX}</a>
			<em>&raquo;</em>&nbsp;<a href="mod.php">{L_MODERATE_PANEL}</a>
		</span>
	</td>
	<td align="right" class="med">
		Показать раздачи со статусом:
		<form name="st" action="{U_ACTION}" id="st">
			<select style="padding-left: 5px !important;" name="st" onchange="$('#st').submit();" id="st">
				<optgroup label="&nbsp;по cтатусу раздачи">
					<option {ST_0} value="0" class="tor-not-approved">* {L_TOR_STATUS_NOT_CHECKED}</option>
					<option {ST_1} value="1" class="tor-closed">x {L_TOR_STATUS_CLOSED}</option>
					<option {ST_2} value="2" class="tor-approved">&radic; {L_TOR_STATUS_CHECKED}</option>
					<option {ST_3} value="3" class="tor-dup">D {L_TOR_STATUS_D}</option>
					<option {ST_4} value="4" class="tor-no-desc">! {L_TOR_STATUS_NOT_PERFECT}</option>
					<option {ST_5} value="5" class="tor-need-edit">? {L_TOR_STATUS_PART_PERFECT}</option>
					<option {ST_6} value="6" class="tor-consumed"># {L_TOR_STATUS_FISHILY}</option>
					<option {ST_7} value="7" class="tor-closed-cp">&copy; {L_TOR_STATUS_COPY}</option>
				</optgroup>
			</select>
		</form>
		<!--
		{L_TOPICS_PER_PAGE}:
		<form id="tpp" method="post">{SELECT_TPP}</form>
		-->
	</td>
</tr>
</table>

<form action="{U_ACTION}" method="post">
<table width="100%" class="forumline tablesorter">
<col class="row1">
<col class="row1">
<col class="row1">
<col class="row1">
<col class="row2">
<col class="row2">
<col class="row2">
<col class="row2">
<thead>
<tr>
	<th class="{sorter: 'text'}"></th>
	<th class="{sorter: 'text'}"></th>
	<th class="{sorter: 'text'}" width="20%"><b class="tbs-text">{L_FORUM}</b></td>
	<th class="{sorter: 'text'}" width="65%"><b class="tbs-text">{L_TOPICS}</b></th>
	<th class="{sorter: 'text'}" width="80"><b class="tbs-text">{L_AUTHOR}</b></th>
	<th class="{sorter: 'digit'}" width="80"><b class="tbs-text">{L_REPLIES}</b></td>
	<th class="{sorter: 'text'}" width="80"><b class="tbs-text">{L_JOINED}</b></td>
	<th class="{sorter: false}" width="20"></th>
</tr>
</thead>
<!-- BEGIN tor -->
<tr class="med tCenter" id="tor_{tor.POST_ID}">
	<td>
		<span style="display: none;">{tor.TOPIC_ICON}</span>
		<img class="topic_icon" src="{tor.TOPIC_ICON}">
	</td>
	<td>
		<!-- IF tor.TOR_STATUS == 0 --><b><span title="{L_TOR_STATUS_NOT_CHECKED}" style="color: purple;">*</span></b><!-- ENDIF -->
		<!-- IF tor.TOR_STATUS == 1 --><b><span title="{L_TOR_STATUS_CLOSED}" style="color: red;">x</span></b><!-- ENDIF -->
		<!-- IF tor.TOR_STATUS == 2 --><b><span title="{L_TOR_STATUS_CHECKED}" style="color: green;">&radic;</span></b><!-- ENDIF -->
		<!-- IF tor.TOR_STATUS == 3 --><b><span title="{L_TOR_STATUS_D}" style="color: blue;">D</span></b><!-- ENDIF -->
		<!-- IF tor.TOR_STATUS == 4 --><b><span title="{L_TOR_STATUS_NOT_PERFECT}" style="color: red;">!</span></b><!-- ENDIF -->
		<!-- IF tor.TOR_STATUS == 5 --><b><span title="{L_TOR_STATUS_PART_PERFECT}" style="color: red;">?</span></b><!-- ENDIF -->
		<!-- IF tor.TOR_STATUS == 6 --><b><span title="{L_TOR_STATUS_FISHILY}" style="color:green;">#</span></b><!-- ENDIF -->
		<!-- IF tor.TOR_STATUS == 7 --><b><span title="{L_TOR_STATUS_COPY}" style="color: red;">&copy;</span></b><!-- ENDIF -->
	</td>
	<td><a href="{tor.U_FORUM}" class="genmed">{tor.FORUM_TITLE}</td>
	<td align="left">
		<a class="folded2 tLink" href="{tor.U_TOPIC}" onclick="ajax.view_post({tor.POST_ID}, this); return false;">Предпросмотр</a>
		<div class="spacer_4"></div>
		<!-- IF tor.IS_UNREAD --><a href="{tor.U_TOPIC}{NEWEST_URL}">{ICON_NEWEST_REPLY}</a><!-- ENDIF -->
		<a class="{tor.DL_CLASS}" href="{tor.U_TOPIC}"><!-- IF tor.TOR_FROZEN -->{tor.TOPIC_TITLE}<!-- ELSE --><b>{tor.TOPIC_TITLE}</b><!-- ENDIF --></a>
		<!-- IF tor.PAGINATION --><span class="topicPG">&nbsp;[{ICON_GOTOPOST}{L_GOTO_SHORT} {tor.PAGINATION} ]</span><!-- ENDIF -->
	</td>
	<td>{tor.TOPIC_POSTER}</td>
	<td>{tor.TOPIC_REPLIES}</td>
	<td><span title="{REG_TIME_BACK}">{tor.REG_TIME}</span></td>
	<td><input type="checkbox" name="topic_id[]" value="{tor.TOPIC_ID}" class="topic-chbox" rel="{tor.POST_ID}"></td>
</tr>
<!-- END tor -->

<!-- BEGIN tor_topics -->
<tfoot>
<tr>
	<td colspan="9" class="catBottom tRight med">
		{L_SELECT}:
		<select style="padding-left: 5px !important;" name="status" onchange="$('#tor-confirm').attr('checked', false); $('#tor-submit').attr('disabled', true)">
			<optgroup label="&nbsp;статус раздачи">
				<option value="0" class="tor-not-approved">* {L_TOR_STATUS_NOT_CHECKED}</option>
				<option value="1" class="tor-closed">x {L_TOR_STATUS_CLOSED}</option>
				<option value="2" selected="selected" class="tor-approved">&radic; {L_TOR_STATUS_CHECKED}</option>
				<option value="3" class="tor-dup">D {L_TOR_STATUS_D}</option>
				<option value="4" class="tor-no-desc">! {L_TOR_STATUS_NOT_PERFECT}</option>
				<option value="5" class="tor-need-edit">? {L_TOR_STATUS_PART_PERFECT}</option>
				<option value="6" class="tor-consumed"># {L_TOR_STATUS_FISHILY}</option>
				<option value="7" class="tor-closed-cp">&copy; {L_TOR_STATUS_COPY}</option>
			</optgroup>
			<optgroup label="&nbsp; Доп. функции">
				<option value="lock">{L_LOCK}</option>
				<option value="unlock">{L_UNLOCK}</option>
				<option value="down">{L_DOWN}</option>
				<option value="undown">{L_UNDOWN}</option>
				<!-- IF SHOW_ADMIN_OPTIONS -->
				<option value="delete">{L_DELETE_TOPIC}</option>
				<option value="tor_delete">{L_TOR_DELETE}</option>
				<!-- ENDIF -->
			</optgroup>
		</select>
		<label>
			<input name="confirm" id="tor-confirm" type="checkbox" value="1" onclick="$('#tor-submit').attr('disabled', !this.checked);">{L_CONFIRM}
		</label>
		<input id="tor-submit" type="submit" value="{L_UPDATE}" class="btn" disabled="disabled">
		<input type="button" class="btn" name="checkButton" value="{L_CHECK_ALL}" onclick="this.value=checkBox(this)">
	</td>
</tr>
</tfoot>
<!-- END tor_topics -->
<!-- BEGIN no_tor_topics -->
<tr>
    <td class="row1 tCenter pad_8" colspan="9">{L_NO_MATCH}</td>
</tr>
<!-- END no_tor_topics -->
</table>
</form>
<!-- BEGIN tor_topics -->
<div class="gensmall tRight bold">{PAGINATION}</div>
<!-- END tor_topics -->