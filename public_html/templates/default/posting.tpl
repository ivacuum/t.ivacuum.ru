
<div class="spacer_12"></div>

<!-- IF TPL_SHOW_NEW_POSTS -->
<!--========================================================================-->

<table class="topic" cellpadding="0" cellspacing="0">
<tr>
	<td colspan="2" class="catTitle td2">{L_NEW_POSTS_PREVIEW}</td>
</tr>
<tr>
	<th class="thHead td1">{L_AUTHOR}</th>
	<th class="thHead td2">{L_MESSAGE}</th>
</tr>
<!-- BEGIN new_posts -->
<tr class="{new_posts.ROW_CLASS}">
	<td width="120" class="poster_info td1">
		<p class="nick" onmouseout="bbcode.refreshSelection(false);" onmouseover="bbcode.refreshSelection(true);" onclick="bbcode.onclickPoster('{new_posts.POSTER_NAME_JS}');">
			<a href="#" onclick="return false;">{new_posts.POSTER_NAME}</a>
		</p>
		<p><img src="{SPACER}" width="120" height="10" alt="" /></p>
	</td>
	<td class="message td2">
		<div class="post_head pad_4">{MINIPOST_IMG_NEW} {new_posts.POST_DATE}</div>
		<div class="post_wrap">{new_posts.MESSAGE}</div>
	</td>
</tr>
<!-- END new_posts -->

</table>

<div class="spacer_12"></div>

<!--========================================================================-->
<!-- ENDIF / TPL_SHOW_NEW_POSTS -->

<!-- IF TPL_PREVIEW_POST -->
<!--========================================================================-->

<table class="forumline">
<tr>
	<th>{L_PREVIEW}</th>
</tr>
<tr>
	<td class="row1"><div class="post_body post_wrap">{PREVIEW_MSG}</div></td>
</tr>
</table>

<div class="spacer_12"></div>

<!--========================================================================-->
<!-- ENDIF / TPL_PREVIEW_POST -->

<p class="nav">
	<a href="{U_INDEX}">{T_INDEX}</a>
	<!-- IF U_VIEW_FORUM --><em>&raquo;</em> <a href="{U_VIEW_FORUM}">{FORUM_NAME}</a><!-- ENDIF -->
	<!-- IF POSTING_TOPIC_ID --><em>&raquo;</em> <a class="normal" href="{TOPIC_URL}{POSTING_TOPIC_ID}">{POSTING_TOPIC_TITLE}</a><!-- ENDIF -->
</p>

<form action="{S_POST_ACTION}" method="post" name="post" onsubmit="return checkForm(this);" {S_FORM_ENCTYPE}>
{S_HIDDEN_FORM_FIELDS}
{ADD_ATTACH_HIDDEN_FIELDS}
{POSTED_ATTACHMENTS_HIDDEN_FIELDS}

<table class="bordered">
<col class="row1">
<col class="row2">

<tbody class="pad_4">
<tr>
	<th colspan="2" class="thHead"><b>{POSTING_TYPE_TITLE}</b></th>
</tr>
<!-- IF POSTING_USERNAME -->
<tr>
	<td><b>{L_USERNAME}</b></td>
	<td>
		<input type="text" name="username" size="25" maxlength="25" tabindex="1" value="{USERNAME}" />&nbsp;
		<input type="submit" name="usersubmit" class="btn" value="{L_FIND_USERNAME}" onclick="window.open('{U_SEARCH_USER}', '_phpbbsearch', 'HEIGHT=250,resizable=yes,WIDTH=400');return false;" />
	</td>
</tr>
<!-- ENDIF -->
<!-- IF POSTING_SUBJECT -->
<tr>
	<td><b>{L_SUBJECT}</b></td>
	<td><input type="text" name="subject" size="90" tabindex="2" value="{SUBJECT}" /></td>
</tr>
<!-- ENDIF -->
</tbody>

<tr>
	<td class="vTop pad_4">
		<p><b>{L_MESSAGE_BODY}</b></p><br />

		<table id="smilies" class="smilies borderless">
		<!-- BEGIN smilies_row -->
		<tr>
			<!-- BEGIN smilies_col -->
			<td><a href="#" onclick="bbcode && bbcode.emoticon('{smilies_row.smilies_col.SMILEY_CODE}'); return false;"><img src="{smilies_row.smilies_col.SMILEY_IMG}" alt="" title="{smilies_row.smilies_col.SMILEY_DESC}" /></a></td>
			<!-- END smilies_col -->
		</tr>
		<!-- END smilies_row -->
		<!-- BEGIN switch_smilies_extra -->
		<tr>
			<td colspan="{S_SMILIES_COLSPAN}"><a href="{U_MORE_SMILIES}" onclick="window.open('{U_MORE_SMILIES}', '_phpbbsmilies', 'HEIGHT=400,resizable=yes,scrollbars=yes,WIDTH=400'); return false;" target="_phpbbsmilies" class="med">{L_MORE_SMILIES}</a></td>
		</tr>
		<!-- END switch_smilies_extra -->
		</table><!--/smilies-->

 </td>
 <td class="vTop pad_0 w100"><!-- INCLUDE posting_editor.tpl --></td>
</tr>

<!-- IF SHOW_VIRTUAL_KEYBOARD --><!-- INCLUDE kb.tpl --><!-- ENDIF -->

<!-- BEGIN switch_type_toggle -->
<tr>
	<td colspan="2" class="row2 tCenter pad_6">{S_TYPE_TOGGLE}</td>
</tr>
<!-- END switch_type_toggle -->

<!-- IF ATTACHBOX --><!-- INCLUDE posting_attach.tpl --><!-- ENDIF -->

<!-- IF POLLBOX --><!-- INCLUDE posting_poll.tpl --><!-- ENDIF -->

</table>
<input type="hidden" id="attach_sig" name="attach_sig" value="checked" />
</form>

<!-- IF TPL_TOPIC_REVIEW -->
<!--========================================================================-->

<div class="spacer_12"></div>

<table class="topic" cellpadding="0" cellspacing="0">
<tr>
	<td colspan="2" class="catTitle td2">{L_TOPIC_REVIEW}</td>
</tr>
<tr>
	<th class="thHead td1">{L_AUTHOR}</th>
	<th class="thHead td2">{L_MESSAGE}</th>
</tr>
<!-- BEGIN review -->
<tr class="{review.ROW_CLASS}">
	<td width="120" class="poster_info td1">
		<p class="nick" onmouseout="bbcode.refreshSelection(false);" onmouseover="bbcode.refreshSelection(true);" onclick="bbcode.onclickPoster('{review.POSTER_NAME_JS}');">
			<a href="#" onclick="return false;">{review.POSTER_NAME}</a>
		</p>
		<p><img src="{SPACER}" width="120" height="10" alt="" /></p>
	</td>
	<td class="message td2">
		<div class="post_head pad_4">{MINIPOST_IMG} {review.POST_DATE}</div>
		<div class="post_wrap">{review.MESSAGE}</div>
	</td>
</tr>
<!-- END review -->

</table>

<div class="spacer_12"></div>

<!--========================================================================-->
<!-- ENDIF / TPL_TOPIC_REVIEW -->
