
<!-- IF TPL_ADD_ATTACHMENT -->
<!--========================================================================-->

<tr>
	<th colspan="2" class="thHead">{L_ADD_ATTACH_TITLE}</th>
</tr>
<tr>
	<td class="pad_4"><b>{L_FILE_NAME}</b></td>
	<td>
		<table class="borderless" cellspacing="0">
		<tr>
			<td class="pad_4">
				<input type="file" name="fileupload" size="45" maxlength="{FILESIZE}" />
				<p class="small nowrap">{L_ADD_ATTACH_EXPLAIN}</p>
				<p><input type="submit" class="btn btn-primary" name="add_attachment" value="{L_ADD_ATTACHMENT}" /></p>
				<input type="hidden" name="filecomment" value="{FILE_COMMENT}" />
			</td>
			<td class="med pad_4" style="padding-left: 12px;"></td>
		</tr>
		</table>
	</td>
</tr>

<!--========================================================================-->
<!-- ENDIF / TPL_ADD_ATTACHMENT -->


<!-- IF TPL_POSTED_ATTACHMENTS -->
<!--========================================================================-->

<tbody class="pad_4">
<tr>
	<th colspan="2" class="thHead">{L_POSTED_ATTACHMENTS}</th>
</tr>
<!-- BEGIN attach_row -->
<tr>
	<td class="row5"><b>{L_FILE_NAME}</b></td>
	<td class="row5"><a class="gen" href="{attach_row.U_VIEW_ATTACHMENT}" target="_blank"><b>{attach_row.FILE_NAME}</b></a></td>
</tr>
<tr>
	<td class="row1">{L_FILE_COMMENT}</td>
	<td class="row1">
		<input type="text" name="comment_list[]" size="45" maxlength="255" value="{attach_row.FILE_COMMENT}" />&nbsp;
		<input class="btn" type="submit" name="edit_comment[{attach_row.ATTACH_FILENAME}]" value="{L_UPDATE_COMMENT}" />
	</td>
</tr>
<tr>
	<td class="row1">{L_OPTIONS}</td>
	<td class="row1">
		<!-- BEGIN switch_update_attachment -->
		<input class="btn" type="submit" name="update_attachment[{attach_row.ATTACH_ID}]" value="{L_UPLOAD_NEW_VERSION}" />&nbsp;
		<!-- END switch_update_attachment -->
		<input class="btn" type="submit" name="del_attachment[{attach_row.ATTACH_FILENAME}]" value="{L_DELETE_ATTACHMENT}" />&nbsp;
		<!-- BEGIN switch_thumbnail -->
		<input class="btn" type="submit" name="del_thumbnail[{attach_row.ATTACH_FILENAME}]" value="{L_DELETE_THUMBNAIL}" />&nbsp;
		<!-- END switch_thumbnail -->
	</td>
</tr>
<tr>
	<td colspan="2" class="spaceRow"><div class="spacer_4"></div></td>
</tr>
<!-- END attach_row -->
</tbody>

<!--========================================================================-->
<!-- ENDIF / TPL_POSTED_ATTACHMENTS -->

