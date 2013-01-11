<!-- IF AJAX_TOPICS -->
<script type="text/javascript">
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
		}	else {
			$post.css({ display: '' });
		}
	}
	$(src).toggleClass('unfolded2');
};

ajax.callback.view_post = function(data) {
	var post_id = data.post_id;
	var $tor = $('#tor_'+post_id);
	window.location.href='#tor_'+post_id;
	$('#post-row tbody')
		.clone()
		.attr({ id: 'post_'+post_id })
		.find('div.post_body').html(data.post_html).end()
		.find('a.tLink').attr({ href: $('a.tLink', $tor).attr('href') }).end()
		.find('a.dLink').attr({ href: $('a.dLink', $tor).attr('href') }).end()
		.insertAfter($tor)
	;
	initPostBBCode('#post_'+post_id);
	var maxH   = screen.height - 290;
	var maxW   = screen.width - 60;
	var $post  = $('div.post_wrap', $('#post_'+post_id));
	var $links = $('div.post_links', $('#post_'+post_id));
	$post.css({ maxWidth: maxW, maxHeight: maxH });
	$links.css({ maxWidth: maxW });
	if ($.browser.msie) {
		if ($post.height() > maxH) { $post.height(maxH); }
		if ($post.width() > maxW)  { $post.width(maxW); $links.width(maxW); }
	}
	ajax.openedPosts[post_id] = true;
};
</script>

<style type="text/css">
.post_wrap { border: 1px #A5AFB4 solid; margin: 8px 8px 6px; overflow: auto; }
.post_links { margin: 6px; }
</style>

<table id="post-row" style="display: none;">
<tbody>
<tr>
	<td class="row2" colspan="{TOR_COLSPAN}">
		<div class="post_wrap row1">
			<div class="post_body pad_6"></div><!--/post_body-->
			<div class="clear"></div>
		</div><!--/post_wrap-->
		<div class="post_links med bold tCenter"><a class="tLink">{L_OPEN_TOPIC}</a> &nbsp;&#0183;&nbsp; <a class="dLink">{L_DL_TORRENT}</a></div>
	</td>
</tr>
</tbody>
</table>
<!-- ENDIF / AJAX_TOPICS -->

<a name="start"></a>
<h1 class="pagetitle">{PAGE_TITLE}</h1>

<div class="nav">
	<p class="floatL"><a href="{U_INDEX}">{T_INDEX}</a></p>
	<!-- IF MATCHES --><p class="floatR">{MATCHES} {SERACH_MAX}</p><!-- ENDIF -->
	<div class="clear"></div>
</div>

<!-- IF TORHELP_TOPICS -->
	<!-- INCLUDE torhelp.tpl -->
	<div class="spacer_6"></div>
<!-- ENDIF / TORHELP_TOPICS -->

<!-- IF SHOW_SEARCH_OPT -->
<style type="text/css"> 
#fs-nav-ul .b { font-weight: bold; }
#fs-nav-ul li, #fs-nav-close { cursor: pointer; }
#fs-nav-ul span.f:hover, #fs-nav-close:hover { color: blue; background: #DEE2E4; }
#fs-nav-list { border: 3px double #9AA7AD; background: #EFEFEF; padding: 8px; max-height: 500px; overflow: auto; }
#fs-sel-cat { min-width: 250px; max-width: 300px; }
#fs-sel-cat option.cat-title { font-weight: bold; color: #005A88; background: #F5F5F5; }
.tablesorter .header { padding: 2px 4px; }
</style> 
 
<script type="text/javascript"> 
var FSN = {
	fs_all      : '',
	fs_og       : [],
	fs_lb       : [],
	show_fs_nav : true,
	sel_width   : null,
	scroll      : $.browser.mozilla,
 
	build_nav: function() {
 
		var $fieldset = $('fieldset#fs');
		var $select = $('select', $fieldset);
		var $optgroup = $('optgroup', $select);
 
		$('legend', $fieldset).empty().append( $('#fs-nav-legend').contents() );
		FSN.show_fs_nav = !($.browser.msie && parseFloat($.browser.version) <= 6);
 
		$optgroup.each(function(i){
			var $og = $(this);
			$og.attr({ id: 'og-'+i });
			FSN.fs_og[i] = $(this).html();
			FSN.fs_lb[i] = $(this).attr('label');
			$('#fs-sel-cat').append('<option class="cat-title" value="'+ i +'">&nbsp;&nbsp;&middot;&nbsp;'+ FSN.fs_lb[i] +'&nbsp;</option>\n');
			if (FSN.show_fs_nav) {
				$('<li><span class="b">'+ FSN.fs_lb[i] +'</span>\n<ul id="nav-c-'+ i +'"></ul>\n</li>').appendTo('#fs-nav-ul').click(function(){
					if (FSN.scroll) {
						$select.scrollTo('#og-'+i);
					}
				});
				$('option', $og).each(function(){
					var $op = $(this);
					if ($op[0].className) {
						$('<li><span class="f">'+ $op.html() +'</span>\n</li>').appendTo('#nav-c-'+ i).click(function(e){
							e.stopPropagation();
							if (FSN.scroll) {
								$select.scrollTo( '#'+$op.attr('id'), { duration:300 } ).scrollTo( '-=3px' );
							}
							$('option', $select).attr({ selected: 0 });
							$('#'+$op.attr('id')).attr({ selected: 1 });
							$('#fs-nav-list').fadeOut();
						});
					}
				});
			}
		});
 
		if (FSN.show_fs_nav) {
			$('#fs-nav-menu').show();
			$('#fs-nav-ul').treeview({ collapsed: true });
			// фикс для FF2, чтобы меню не выстраивалось лесенкой
			if ($.browser.mozilla && parseFloat($.browser.version) <= 1.8) {
				$('#fs-nav-list ul').after('<div style="margin-top: 1px;"></div>');
			}
		}
		else {
			$('#fs-nav-menu').remove();
		}
 
		$('#fs-sel-cat').bind('change', function(){
			var i = $(this).val();
			if (FSN.sel_width == null) {
				FSN.sel_width = $select.width() + 4;
			}
			// опера не понимает <optgroup> при популяции селекта [http://dev.jquery.com/ticket/3040]
			if ($.browser.opera) {
				if (i == 'all') {
					$select.empty().append('<option id="fs--1" value="-1">&nbsp;Все имеющиеся</option>\n');
					$.each(FSN.fs_og, function(i, v){
					 $select.append( $(document.createElement('optgroup')).attr('label', FSN.fs_lb[i]).append(FSN.fs_og[i]) );
					});
				}
				else {
					$select.empty().append( $(document.createElement('optgroup')).attr('label', FSN.fs_lb[i]).append(FSN.fs_og[i]) );
				}
			}
			else {
				if (i == 'all') {
					var fs_html = FSN.fs_all;
				}
				else {
					var fs_html = '<optgroup label="'+ FSN.fs_lb[i] +'">'+ FSN.fs_og[i] +'</optgroup>';
				}
				$select.html(fs_html).focus();
			}
			if (i == 'all') {
				$('#fs-nav-menu').show();
			}
			else {
				$('#fs-nav-menu').hide();
			}
			$select.width(FSN.sel_width);
		});
 
		FSN.fs_all = $select.html();
	}
};
 
$(function(){
	FSN.build_nav();
});
</script>

<div class="menu-sub" id="fs-nav-list">
	<div class="tRight"><span id="fs-nav-close" class="med" onclick="$('#fs-nav-list').hide();"> [ {L_HIDE} ] </span></div>
	<ul id="fs-nav-ul" class="tree-root"></ul>
</div>

<div id="fs-nav-legend" style="display: none;"> 
	<select id="fs-sel-cat"><option value="all">&nbsp;Выбрать категорию...&nbsp;</option></select> 
	<span id="fs-nav-menu" style="display: none;">&middot;&nbsp;<a class="menu-root" href="#fs-nav-list">перейти к разделу</a></span> 
</div>

<form method="POST" name="post" action="{TOR_SEARCH_ACTION}">
{S_HIDDEN_FIELDS}
<input type="hidden" name="da" value="1" />
<input type="hidden" name="df" value="1" />

<table class="bordered w100" cellspacing="0">
<col class="row1">
<tr>
	<th class="thHead">Поиск по раздачам</th>
</tr>
<tr>
	<td class="row4" style="padding: 4px";>

		<table class="fieldsets borderless bCenter pad_0" cellspacing="0">
		<tr>
			<td rowspan="2" width="50%">
				<fieldset id="fs">
				<legend>Искать в форумах</legend>
				<!--
					<select id="fs-sel-cat"><option value="all">&nbsp;{L_SELECT_CAT}&nbsp;</option></select>
					<span id="fs-nav-menu" style="display: none">&middot;&nbsp;<a class="menu-root" href="#fs-nav-list">{L_GO_TO_SECTION}</a></span>
				-->
				<div>
					<p class="select">{CAT_FORUM_SELECT}</p>
					<p id="fs-qs-div" class="med" style="display: none;"><input id="fs-qs-input" type="text" style="width: 200px;"> <i>фильтр по названию</i></p>
					<p><img width="300" class="spacer" src="{SPACER}" alt="" /></p>
				</div>
				</fieldset>
			</td>
			<td height="1" width="20%">
				<fieldset>
				<legend>{L_SORT_BY}</legend>
				<div class="med">
					<p class="select">{ORDER_SELECT}</p>
					<p class="radio"><label><input type="radio" name="{SORT_NAME}" value="{SORT_ASC}" {SORT_ASC_CHECKED} /> {L_ASC}</label></p>
					<p class="radio"><label><input type="radio" name="{SORT_NAME}" value="{SORT_DESC}" {SORT_DESC_CHECKED} /> {L_DESC}</label></p>
				</div>
				</fieldset>
				<fieldset>
				<legend>{L_TORRENTS_FROM}</legend>
				<div>
					<p class="select dis-if-ts">{TIME_SELECT}</p>
				</div>
				</fieldset>
				<!--
				<fieldset>
				<legend>{L_SEED_NOT_SEEN}</legend>
				<div>
					<p class="select">{S_NOT_SEEN_SELECT}</p>
				</div>
				</fieldset>
				-->
			</td>
			<td width="30%">
				<fieldset>
				<legend>{L_SHOW_ONLY}</legend>
				<div class="gen">
					<p class="chbox dis-if-ts">{ONLY_MY_CHBOX}[<b>&reg;</b>]</p>
					<p>{DL_WILL_CHBOX}</p>
					<p>{DL_COMPL_CHBOX}</p>
					<!-- <p class="chbox">{ONLY_ACTIVE_CHBOX}</p> -->
					<p class="chbox">{SEED_EXIST_CHBOX}</p>
					<p class="chbox dis-if-ts">{ONLY_NEW_CHBOX}[{MINIPOST_IMG_NEW}]&nbsp;</p>
					<p class="chbox"><label><input type="checkbox" onclick="user.set('h_tsp', this.checked ? 1 : 0);" />&nbsp;Скрыть содержимое {...}</label></p>
				</div>
				</fieldset>
				<!--
				<fieldset>
				<legend>{L_MY_DOWNLOADS}</legend>
				<div>
					<table class="borderless my_downloads" cellspacing="0">
					<tr>
						<td>{DL_COMPL_CHBOX}</td>
						<td>{DL_WILL_CHBOX}</td>
					</tr>
					<tr>
						<td>{DL_DOWN_CHBOX}</td>
						<td>{DL_CANCEL_CHBOX}</td>
					</tr>
					</table>
				</div>
				</fieldset>
				-->
			</td>
		</tr>
		<tr>
			<td colspan="2" width="50%">
				<!--
				<fieldset style="margin-top: 0;">
				<legend>{L_SHOW_COLUMN}</legend>
				<div>
					<p class="chbox">{SHOW_CAT_CHBOX}&nbsp; {SHOW_FORUM_CHBOX}&nbsp; {SHOW_AUTHOR_CHBOX}&nbsp; {SHOW_SPEED_CHBOX}&nbsp;</p>
				</div>
				</fieldset>
				-->
				<fieldset style="margin-top: 0; padding-bottom: 3px;">
				<legend>{L_AUTHOR}</legend>
				<div>
					<p class="input dis-if-ts"><input style="width: 40%" <!-- IF POSTER_ERROR -->style="color: red"<!-- ELSE --> class="post"<!-- ENDIF --> type="text" size="16" maxlength="{POSTER_NAME_MAX}" name="{POSTER_NAME_NAME}" value="{POSTER_NAME_VAL}" /> <button class="btn-small" onclick="window.open('{U_SEARCH_USER}', '_phpbbsearch', 'HEIGHT=250,resizable=yes,WIDTH=400'); return false;">{L_FIND_USERNAME}</button></p>
				</div>
				</fieldset>
				<fieldset style="margin-top: 4px; padding-bottom: 3px;">
				<legend>{L_TITLE_MATCH}</legend>
				<div>
					<p class="input">
						<input style="width: 95%;" class="post" id="title-search" type="text" size="50" maxlength="{TITLE_MATCH_MAX}" name="{TITLE_MATCH_NAME}" value="{TITLE_MATCH_VAL}" />
					</p>
					<p class="chbox med" style="padding-top: 3px;">
						<!--{ALL_WORDS_CHBOX}&nbsp;&middot;&nbsp;-->
						<a class="med s-all" href="search.php">Поиск по форуму</a>
					</p>
				</div>
				</fieldset>
				<fieldset style="margin-top: 4px; padding-bottom: 4px;">
				<legend>Ссылки</legend>
				<div>
					<p class="chbox med" style="padding-top: 3px;">
						<a class="med" href="#" onclick="return get_fs_link();">Ссылка на выбранные разделы</a> &nbsp;&middot;&nbsp;
						<a class="med" href="{TOPIC_URL}{$bb_cfg['search_match_help_topic_id']}">Помощь по поиску</a>
					</p>
				</div>
				</fieldset>
			</td>
		</tr>
		</table>

	</td>
</tr>
<tr>
	<td class="row3 pad_4 tCenter">
		<input class="btn btn-primary" type="submit" name="submit" value="&nbsp;&nbsp;{L_SEARCH}&nbsp;&nbsp;" />
	</td>
</tr>
</table>

</form>

<div class="spacer_6"></div>

<!-- ENDIF / SHOW_SEARCH_OPT -->

<script type="text/javascript"> 
$(function() {
	$('#title-search').bind('keyup blur mouseleave', function() {
		var disabled = (this.value != '');
		ts_dis_selects(disabled);
	});
	ts_dis_selects($('#title-search').val() != '');
});

function ts_dis_selects(disabled) {
	$('p.dis-if-ts').find('select, input').attr('disabled',  disabled ? 1 : 0);
}
</script>

<table class="w100 border bw_TRL" cellpadding="0" cellspacing="0">
	<tr>
		<td class="cat pad_2">
			<table cellspacing="0" cellpadding="0" class="borderless w100">
				<tr>
					<td class="small bold nowrap tRight" width="100%" style="padding: 2px 8px 5px 4px;">
						&nbsp;
						<!-- IF LOGGED_IN -->
						<a class="menu-root" href="#tr-options">{L_DISPLAYING_OPTIONS}</a>
						<!-- ENDIF / LOGGED_IN -->
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<!--
<div class="menu-sub" id="tr-options">
	<table cellspacing="1" cellpadding="4">
		<tr>
			<th>{L_DISPLAYING_OPTIONS}</th>
		</tr>
		<tr>
			<td>
				<fieldset>
				<legend>{L_OPEN_TOPICS}</legend>
				<div class="med pad_4">
					<label><input type="checkbox" checked="checked" onclick="user.set('hl_tr', this.checked ? 1 : 0);" />подсвечивать строки над курсором</label>
				</div>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td class="cat tCenter pad_4"><input type="button" value="Отправить" style="width: 100px;" onclick="window.location.reload();"></td>
		</tr>
	</table>
</div>
-->

<!-- IF LOGGED_IN -->
<div class="menu-sub" id="tr-options">
	<table cellspacing="1" cellpadding="4">
	<tr>
		<th>{L_DISPLAYING_OPTIONS}</th>
	</tr>
	<tr>
		<td>
			<fieldset id="ajax-topics">
			<legend>{L_OPEN_TOPICS}</legend>
			<div class="med pad_4">
				<label>
					<input type="checkbox" <!-- IF AJAX_TOPICS -->{CHECKED}<!-- ENDIF -->
						onclick="user.set('tr_t_ax', this.checked ? 1 : 0);"
					/>{L_OPEN_IN_SAME_WINDOW}
				</label>
			</div>
			</fieldset>
		</td>
	</tr>
	<tr>
		<td class="cat tCenter pad_4"><input type="button" value="{L_DO_SUBMIT}" style="width: 100px;" onclick="window.location.reload();" /></td>
	</tr>
	</table>
</div><!--/tr-options-->
<!-- ENDIF / LOGGED_IN -->

<style type="text/css"> 
#tor-tbl u { display: none; }
.seed-leech { padding-left: 1px; padding-right: 0; }
.tr_tm { margin-top: 2px; font-size: 10px; color: #676767; }
.ch { font-style: italic; color: #0080FF; }
.tor-size { padding: 7px 4px 6px !important; }
tr.hl-tr:hover td { background-color: #F8F8F8 !important; }</style> 
<script type="text/javascript"> 
$(function(){
	$('#tor-tbl').tablesorter();
});
</script>

<table class="forumline tablesorter" id="tor-tbl">
<thead>
<tr>
	<th class="{sorter: false}">&nbsp;</th>
	<th class="{sorter: false}">&nbsp;</th>
	<th class="{sorter: 'text'}" width="25%"><b class="tbs-text">Форум</b></th>
	<th class="{sorter: 'text'}" width="75%"><b class="tbs-text">Тема</b></th>
	<th class="{sorter: 'text'}"><b class="tbs-text">Автор</b></th>
	<th class="{sorter: false}">DL</th>
	<th class="{sorter: 'digit'}"><b class="tbs-text">Размер</b></th>
	<th class="seed-leech {sorter: 'digit'}" title="{L_SEEDERS}"><b class="tbs-text">S</b></th>
	<th class="seed-leech {sorter: 'digit'}" title="{L_LEECHERS}"><b class="tbs-text">L</b></th>
	<th class="{sorter: 'digit'}" title="Торрент скачан"><b class="tbs-text">C</b></th>
	<th class="{sorter: 'digit'}"><b class="tbs-text">Добавлен</b></th>
</tr>
</thead>
<tbody>
<!-- BEGIN tor -->
<tr class="tCenter hl-tr" id="tor_{tor.POST_ID}">
	<td class="row1"><!-- IF tor.USER_AUTHOR --><p style="padding-bottom: 3px">&nbsp;<b>&reg;</b>&nbsp;</p><!-- ELSEIF tor.IS_NEW -->{MINIPOST_IMG_NEW}<!-- ELSE -->{MINIPOST_IMG}<!-- ENDIF --></td>
	<td class="row1"><!-- IF tor.TOR_STATUS == 0 --><b><span title="{L_TOR_STATUS_NOT_CHECKED}" style="color: purple;">*</span></b><!-- ENDIF -->
			<!-- IF tor.TOR_STATUS == 1 --><b><span title="{L_TOR_STATUS_CLOSED}" style="color: red;">x</span></b><!-- ENDIF -->
			<!-- IF tor.TOR_STATUS == 2 --><b><span title="{L_TOR_STATUS_CHECKED}" style="color: green;">&radic;</span></b><!-- ENDIF -->
			<!-- IF tor.TOR_STATUS == 3 --><b><span title="{L_TOR_STATUS_D}" style="color: blue;">D</span></b><!-- ENDIF -->
			<!-- IF tor.TOR_STATUS == 4 --><b><span title="{L_TOR_STATUS_NOT_PERFECT}" style="color: red;">!</span></b><!-- ENDIF -->	
			<!-- IF tor.TOR_STATUS == 5 --><b><span title="{L_TOR_STATUS_PART_PERFECT}" style="color: red;">?</span></b><!-- ENDIF -->
			<!-- IF tor.TOR_STATUS == 6 --><b><span title="{L_TOR_STATUS_FISHILY}" style="color:green;">#</span></b><!-- ENDIF -->
			<!-- IF tor.TOR_STATUS == 7 --><b><span title="{L_TOR_STATUS_COPY}" style="color: red;">&copy;</span></b><!-- ENDIF -->
	</td>
	<!-- IF SHOW_CAT -->
	<td class="row1"><a class="gen" href="{TR_CAT_URL}{tor.CAT_ID}">{tor.CAT_TITLE}</a></td>
	<!-- ENDIF -->
	<!-- IF SHOW_FORUM -->
	<td class="row1"><a class="gen f" href="{TR_FORUM_URL}{tor.FORUM_ID}">{tor.FORUM_NAME}</a></td>
	<!-- ENDIF -->
	<td class="row4 med tLeft u">
		<div>
			<a class="tLink {tor.DL_CLASS}<!-- IF AJAX_TOPICS --> folded2<!-- ENDIF -->" <!-- IF AJAX_TOPICS -->onclick="ajax.view_post({tor.POST_ID}, this); return false;"<!-- ENDIF --> href="{TOPIC_URL}{tor.TOPIC_ID}"><!-- IF tor.TOR_FROZEN -->{tor.TOPIC_TITLE}<!-- ELSE --><b>{tor.TOPIC_TITLE}</b><!-- ENDIF --></a>
		</div>
	</td>
	<!-- IF SHOW_AUTHOR -->
	<td class="row1"><a class="med" href="{TR_POSTER_URL}{tor.POSTER_ID}">{tor.USERNAME}</a></td>
	<!-- ENDIF -->
	<td class="row4 med nowrap"><!-- IF tor.TOR_FROZEN -->&mdash;<!-- ELSE -->&nbsp;<a class="med dLink" href="{DOWNLOAD_URL}{tor.ATTACH_ID}"><img src="{STATIC_PATH}/i/_/drive_download.png" alt="" />&nbsp;</a><!-- ENDIF --></td>
	<td class="row4 small nowrap tor-size"><u>{tor.TOR_SIZE_RAW}</u>{tor.TOR_SIZE}</td>
	<td class="row4 seedmed" title="{L_SEEDERS}"><b>{tor.SEEDS}</b></td>
	<td class="row4 leechmed" title="{L_LEECHERS}"><b>{tor.LEECHS}</b></td>
	<td class="row4 small">{tor.COMPLETED}</td>
	<!-- IF SHOW_SPEED -->
	<td class="row4 nowrap">
		<p class="seedmed">{tor.UL_SPEED}</p>
		<p class="leechmed">{tor.DL_SPEED}</p>
	</td>
	<!-- ENDIF -->
	<td class="row4 small nowrap" style="padding: 1px 3px 2px;" title="{L_ADDED}">
		<u>{tor.ADDED_RAW}</u>
		<p>{tor.ADDED_DATE}</p>
		<!-- IF tor.HOT -->
		<p>{tor.ADDED_TIME}</p>
		<!-- ENDIF -->
	</td>
</tr>
<!-- END tor -->
</tbody>
<!-- IF TOR_NOT_FOUND -->
<tr>
	<td class="row1 tCenter pad_8" colspan="{TOR_COLSPAN}">{NO_MATCH_MSG}</td>
</tr>
<!-- ENDIF / TOR_NOT_FOUND -->
<tfoot>
<tr>
	<td class="catBottom" colspan="{TOR_COLSPAN}">&nbsp;</td>
</tr>
</tfoot>
</table>

<div class="bottom_info">

	<div class="nav">
		<p style="float: left">{PAGE_NUMBER}</p>
		<p style="float: right">{PAGINATION}</p>
		<div class="clear"></div>
	</div>

</div><!--/bottom_info-->

<script type="text/javascript"> 
// Скрыть содержимое {...}
if( user.opt_js.h_tsp ) {
	$('a.tLink').each(function() {
		$(this).html( $(this).html().replace(/\{.+?\}/g, '{...}') );
	});
}
$.each([-1], function(i,n){ $('#fs-'+ n).attr('selected', 1) });
$('#search_opt, #search-results').show();

var fs_last_val = [];

$(function() {
	// лимит на количество выбранных разделов
	fs_last_val = $('#fs-main').val();

	$('#fs-main').bind('change', function() {
		var fs_val = $('#fs-main').val();

		if( fs_val != null ) {
			if( fs_val.length > 50 ) {
				alert('Вы можете выбрать максимум 50 разделов');
				$('#fs-main').val(fs_last_val);
			} else {
				fs_last_val = fs_val;
			}
		}
	});

	if( $.browser.mozilla ) {
		$('#fs-qs-input').focus().quicksearch('#fs-main option', {
			delay   : 300,
			onAfter : function(){
				$('#fs-main optgroup').show();
				$('#fs-main option:hidden').parent('optgroup').not( $('#fs-main :visible').parent('optgroup') ).hide();
			}
		});
		$('#fs-main').attr('size', $('#fs-main').attr('size') - 1);
		$('#fs-qs-div').show();
	}
});

function get_fs_link()
{
	var fs_url = 'http://t.ivacuum.ru/tracker.php?';
	var fs_val = $('#fs-main').val();

	if( fs_val == null ) {
		alert('Вы не выбрали разделы');
	} else {
		fs_url += 'f[]='+ fs_val.join('&f[]=');
		window.prompt('Ссылка на выбранные разделы:', fs_url);
	}

	return false;
}
</script> 