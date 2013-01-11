
<!-- IF SIMPLE_FOOTER -->

<!-- ELSEIF IN_ADMIN -->

<!-- ELSE -->

	</div><!--/main_content_wrap-->
	</td><!--/main_content-->

	<!-- IF SHOW_SIDEBAR2 -->
		<!--sidebar2-->
		<td id="sidebar2">
		<div id="sidebar2_wrap">
			<?php if (!empty($bb_cfg['sidebar2_static_content_path'])) include($bb_cfg['sidebar2_static_content_path']); ?>
			<img width="210" class="spacer" src="{SPACER}" alt="" />
		</div><!--/sidebar2_wrap-->
		</td><!--/sidebar2-->
	<!-- ENDIF -->

	</tr></table>
	</div>
	<!--/page_content-->

	<!--page_footer-->
	<div id="page_footer">
		<!-- IF SHOW_ADMIN_LINK -->
		<div class="tCenter">[ <a href="{ADMIN_LINK_HREF}">{L_GOTO_ADMINCP}</a> ]</div>
		<br />
		<!-- ENDIF -->
	</div>

	<!--/page_footer -->

	</div>
	<!--/page_container -->

<!-- ENDIF -->

<!-- IF ONLOAD_FOCUS_ID -->

<script type="text/javascript">
$p('{ONLOAD_FOCUS_ID}').focus();
</script>

<!-- ENDIF -->