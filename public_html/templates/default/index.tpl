<!-- IF TORHELP_TOPICS -->
	<!-- INCLUDE torhelp.tpl -->
	<div class="spacer_6"></div>
<!-- ENDIF / TORHELP_TOPICS -->

<div id="forums_list_wrap">

<div id="forums_top_nav">
	<h1 class="pagetitle"><a href="{U_INDEX}">{T_INDEX}</a></h1>
</div><!--/forums_top_nav-->

<!-- IF CHAT_ALLOWED -->
<h3 class="chat-title" rel="toggle_chat" style="cursor: pointer;">Чатец</h3>
<div id="chat">
	<div id="chat_messages" class="shadow-light"></div>
	<div style="font-size: 12px;" class="chat-bottom">
		<form name="post" id="chat_form">
			Сообщение: <input tabindex="2" name="chat_message" id="message" style="font-size: 12px; vertical-align: middle; width: 35.7em; border: 1px solid #ccc; padding: 3px; border-right: 0;"><button class="btn btn-left btn-primary btn-icons" id="chat_post"><img src="{STATIC_PATH}/i/_/balloon.png" alt="">Отправить</button><button class="btn btn-right btn-icons" id="chat_smilies">&nbsp;<img src="{STATIC_PATH}/i/_/smiley_mr_green.png" alt=""></button>
		</form>
	</div>
</div>
<!-- ENDIF -->

<!-- IF LOGGED_IN -->
<div id="forums_top_links">
	<div class="floatL">
		<a class="btn btn-left btn-primary btn-orange btn-icons" href="{U_SEARCH_NEW}"><img src="{STATIC_PATH}/i/_/balloons.png" alt="" />{L_SEARCH_NEW}</a><!-- IF SHOW_MODER_OPTIONS --><a class="btn btn-middle btn-positive btn-icons" href="mod.php"><img src="{STATIC_PATH}/i/_/tick.png" alt="" />Проверить раздачи</a><!-- ENDIF --><a href="{U_SEARCH_SELF_BY_LAST}" class="btn btn-middle btn-icons"><img src="{STATIC_PATH}/i/_/mails_stack.png" alt="" />{L_SEARCH_SELF}</a><a class="btn btn-right btn-icons" href="{U_SEARCH_LATEST}"><img src="{STATIC_PATH}/i/_/balloons_white.png" alt="" />{L_SEARCH_LATEST} сообщения</a>
	</div>
	<div class="floatR med bold">
		<a class="btn menu-root" href="#only-new-options">{L_DISPLAYING_OPTIONS}</a>
	</div>
	<div class="clear"></div>
</div><!--/forums_top_links-->

<div class="menu-sub" id="search-my-posts">
	<table cellspacing="1" cellpadding="4">
	<tr>
		<th>{L_SEARCH_SELF}</th>
	</tr>
	<tr>
		<td>
			<fieldset id="search-my">
			<legend>{L_SORT_BY}</legend>
			<div class="bold nowrap pad_2">
				<p class="mrg_4"><a class="med" href="{U_SEARCH_SELF_BY_LAST}">{L_SEARCH_SELF_BY_LAST}</a></p>
				<p class="mrg_4"><a class="med" href="{U_SEARCH_SELF_BY_MY}">{L_SEARCH_SELF_BY_MY}</a></p>
			</div>
			</fieldset>
		</td>
	</tr>
	</table>
</div><!--/search-my-posts-->
<!-- ENDIF -->

<img width="540" class="spacer" src="{SPACER}" alt="" />

<div id="forums_wrap">

<!-- IF SHOW_FORUMS -->

<!-- BEGIN c -->
<div class="cats shadow-light">
	<h2><a href="{c.U_VIEWCAT}">{c.CAT_TITLE}</a></h2>
	<table cellpadding="2" cellspacing="0" width="100%">
		<!-- BEGIN f -->
		<tr class="cats_forum">
			<td class="f_icon"><a href="search.php?f={c.f.FORUM_ID}&amp;new=1&amp;dm=1&amp;s=0&amp;o=1"><img class="forum_icon" src="{c.f.FORUM_FOLDER_IMG}" alt="{c.f.FORUM_FOLDER_ALT}" /></a></td>
			<td class="f_titles">
				<h4 class="forumlink"><a href="{FORUM_URL}{c.f.FORUM_ID}">{c.f.FORUM_NAME}</a></h4>
				<!-- IF c.f.FORUM_DESC --><p class="forum_desc">{c.f.FORUM_DESC}</p><!-- ENDIF -->
				<!-- IF c.f.LAST_SF_ID -->
				<p class="subforums">
					<!-- BEGIN sf -->
					<span class="sf_title<!-- IF c.f.sf.NEW --> new_posts<!-- ENDIF -->">• <a href="{FORUM_URL}{c.f.sf.SF_ID}">{c.f.sf.SF_NAME}</a></span><!-- IF c.f.sf.SF_ID == c.f.LAST_SF_ID --><!-- ELSE --><!-- ENDIF -->
					<!-- END sf -->
				</p>
				<!-- ENDIF -->
			</td>
			<td align="center" class="f_last_post row2">
				<!-- IF c.f.REDIRECTS gt 0 -->
				<p class="f_stat_inline">Переходов: {c.f.REDIRECTS}</p>
				<!-- ELSE -->
				<!-- IF c.f.POSTS -->
				<!-- BEGIN last -->
				<h6 class="last_topic"><a href="{TOPIC_URL}{c.f.last.LAST_TOPIC_ID}{NEWEST_URL}" title="{c.f.last.LAST_TOPIC_TIP}">{c.f.last.LAST_TOPIC_TITLE}</a></h6>
				<p class="last_post_time"><span class="last_time">{c.f.last.LAST_POST_TIME}</span><span class="last_author">by <!-- IF c.f.last.LAST_POST_USER_ID --><a href="{PROFILE_URL}{c.f.last.LAST_POST_USER_ID}">{c.f.last.LAST_POST_USER_NAME}</a><!-- ELSE -->{c.f.last.LAST_POST_USER_NAME}<!-- ENDIF --></span></p>
				<!-- END last -->
				<p class="f_stat_inline"><em>Тем:</em> {c.f.TOPICS} <em>Сообщ.:</em> {c.f.POSTS}</p>
				<!-- ELSE / start of !c.f.POSTS -->
				<p class="f_stat_inline">{L_NO_POSTS}</p>
				<!-- ENDIF -->
				<!-- ENDIF -->
			</td>
		</tr>
		<!-- END f -->
	</table>
</div>
<div class="cat_separator"></div>
<!-- END c -->

<!-- ELSE / start of !SHOW_FORUMS -->

<table class="forumline">
	<tr><td class="row1 tCenter pad_8">{NO_FORUMS_MSG}</td></tr>
</table>
<div class="spacer_10"></div>

<!-- ENDIF -->

</div><!--/forums_wrap-->

<div id="forums_footer"></div>

<!-- IF LOGGED_IN and SHOW_FORUMS -->
<div id="mark_all_forums_read" style="float: left; margin-top: -7px;"><a class="btn btn-small" href="{U_INDEX}" class="med" onclick="setCookie('{COOKIE_MARK}', 'all_forums');">{L_MARK_ALL_FORUMS_READ}</a></div>
<!-- ENDIF -->
<div id="mark_all_forums_read" style="color: #333; text-align: right; float: right; margin-top: -7px;">{CURRENT_TIME}<br />{LAST_VISIT_DATE}</div>

<br clear="all" />

<div class="whosonline shadow-light">
	<h2><a href="{U_VIEWONLINE}">{L_WHOSONLINE}</a></h2>
	<table cellpadding="4" cellspacing="0" width="100%">
		<tr>
			<td align="center"><img class="forum_icon" src="{IMG}whosonline.gif" alt="" /></td>
			<td width="100%">
				<p>{TOTAL_USERS_ONLINE}.<!-- IF SHOW_ADMIN_OPTIONS -->&nbsp;{USERS_ONLINE_COUNTS}<!-- ENDIF --></p>
				<p>{RECORD_USERS}.</p>
				<p>Максимальное количество посетителей за сутки (<b>{MAXIMUM_VISITORS}</b>) зафиксировано {MAXIMUM_VISITORS_DATE}.</p>
				<hr class="dashed" />
				<p>{LOGGED_IN_USER_LIST}.</p>
				<hr class="dashed" />
				<div style="float: left;"><p id="online_time">Данные за последние 5 минут.</p></div><div style="float: right;"><p>[ <span class="colorAdmin"><b>{L_ONLINE_ADMIN}</b></span> ] [ <span class="colorMod"><b>{L_ONLINE_MOD}</b></span> ] [ <span class="colorGroup"><b>{L_ONLINE_GROUP_MEMBER}</b></span> ]</p></div>
			</td>
		</tr>
	</table>
</div>

<div class="cat_separator"></div>

<div class="whosonline shadow-light">
	<h2><a href="{U_VIEWONLINE}">Кто сегодня посетил трекер</a></h2>
	<table cellpadding="4" cellspacing="0" width="100%">
		<tr>
			<td align="center"><img class="forum_icon" src="{IMG}whosonline.gif" alt="" /></td>
			<td width="100%">
				<p rel="toggle_users_today" style="cursor: pointer;">Сегодня трекер посетило: <b>{SU_VISITORS}</b> чел. (кликните для просмотра списка).</p>
				<div id="users_today" style="display: none;"></div>
			</td>
		</tr>
	</table>
</div>

<div class="cat_separator"></div>

<div class="whosonline shadow-light">
	<h2><a href="{U_STATS}">Статистика</a></h2>
	<table cellpadding="4" cellspacing="0" width="100%">
		<tr>
			<td align="center"><img class="forum_icon" src="{IMG}whosonline.gif" alt="" /></td>
			<td width="100%">
				<p>Сообщений: <b>{TOTAL_POSTS}</b> | Пользователей: <b>{TOTAL_USERS}</b> | {NEWEST_USER}</p>
				<hr class="dashed" />
				<p><span class="seed">Раздающих: <b>{SU_ACTIVE_SEEDERS}</b></span> | <span class="leech">Качающих: <b>{SU_ACTIVE_LEECHERS}</b></span><!-- IF SU_SPEED gt 0 --> | Скорость обмена: <a href="{U_TOP_SPEED}"><b>{SU_SPEED}</b></a><!-- ENDIF --> | Суммарный трафик: <img src="{STATIC_PATH}/i/_/traffic_light.png" alt="" style="vertical-align: text-top;"> <b>{SU_TOTAL_DL_UL}</b></p>
				<hr class="dashed" />
				<p>Зарегистрировано торрентов: <b>{SU_ALL_TOR}</b> (объёмом <img src="{STATIC_PATH}/i/_/chart_pie.png" alt="" style="vertical-align: text-top;"> <b>{SU_ALL_TOR_SIZE}</b>) | Из них активных: <b>{SU_ACTIVE_TOR}</b></p>
			</td>
		</tr>
	</table>
</div>

<br />

<div class="spacer_4"></div>

<!--bottom_info-->
<div class="bottom_info">
	<table class="bCenter med" id="f_icons_legend">
	<tr>
		<td><img class="forum_icon" src="{IMG}folder_new_big.gif" alt="new"/></td>
		<td>{L_NEW_POSTS}</td>
		<td><img class="forum_icon" src="{IMG}folder_big.gif" alt="old" /></td>
		<td>{L_NO_NEW_POSTS}</td>
		<td><img class="forum_icon" src="{IMG}folder_locked_big.gif" alt="locked" /></td>
		<td>{L_FORUM_LOCKED}</td>
	</tr>
	</table>

</div><!--/bottom_info-->

</div><!--/forums_list_wrap-->

<script type="text/javascript">
$(document).ready(function() {
  var lsa = ( typeof(localStorage) != 'undefined' ) ? true : false;

  var chat_refresh;
  var display_chat     = 'block';
  var display_ratio    = 'block';
  var display_feedback = 'block';
  var display_forecast = 'none';
  var display_currency = 'none';
  var display_afisha   = 'none';

  if( lsa ) {
    display_chat     = localStorage.getItem('chat') ? localStorage.getItem('chat') : display_chat;
    display_ratio    = localStorage.getItem('ratio') ? localStorage.getItem('ratio') : display_ratio;
    display_feedback = localStorage.getItem('feedback') ? localStorage.getItem('feedback') : display_feedback;
    display_forecast = localStorage.getItem('forecast') ? localStorage.getItem('forecast') : display_forecast;
    display_currency = localStorage.getItem('currency') ? localStorage.getItem('currency') : display_currency;
    display_afisha   = localStorage.getItem('afisha') ? localStorage.getItem('afisha') : display_afisha;
  }

  $('#chat').css('display', display_chat);
  $('#ratio').css('display', display_ratio);
  $('#feedback').css('display', display_feedback);
  $('#forecast').css('display', display_forecast);
  $('#currency').css('display', display_currency);
  $('#afisha').css('display', display_afisha);

  var top_releasers_loaded = false;
  var top_share_loaded = false;

  ajax.view_top_releasers = function() {
    ajax.exec({
      action: 'view_top_releasers'
    });
  };

  ajax.callback.view_top_releasers = function(data) {
    $('#top_releasers').append(data.html).slideToggle('slow');
    top_releasers_loaded = true;
  };

  ajax.view_top_share = function() {
    ajax.exec({
      action: 'view_top_share'
    });
  };

  ajax.callback.view_top_share = function(data) {
    $('#top_share').append(data.html).slideToggle('slow');
    top_share_loaded = true;
  };

  $('h3[rel="toggle_top_releasers"]').click(function() {
    if( top_releasers_loaded == false ) {
      ajax.view_top_releasers();
    } else {
    $('#top_releasers').slideToggle('slow');
    }
  });

  $('h3[rel="toggle_top_share"]').click(function() {
    if( top_share_loaded == false ) {
      ajax.view_top_share();
    } else {
    $('#top_share').slideToggle('slow');
    }
  });
  
  $('h3[rel="toggle_ratio"]').bind('click', function() {
    $('#ratio').slideToggle('slow');

    if( lsa ) {
      display_ratio = ( display_ratio == 'block' ) ? 'none' : 'block';
      localStorage.setItem('ratio', display_ratio);
    }
  });

  $('h3[rel="toggle_feedback"]').bind('click', function() {
    $('#feedback').slideToggle('slow');

    if( lsa ) {
      display_feedback = ( display_feedback == 'block' ) ? 'none' : 'block';
      localStorage.setItem('feedback', display_feedback);
    }
  });

  $('h3[rel="toggle_forecast"]').click(function() {
    $('#forecast').slideToggle('slow');

    if( lsa ) {
      display_forecast = ( display_forecast == 'block' ) ? 'none' : 'block';
      localStorage.setItem('forecast', display_forecast);
    }
  });

  $('h3[rel="toggle_currency"]').click(function() {
    $('#currency').slideToggle('slow');

    if( lsa ) {
      display_currency = ( display_currency == 'block' ) ? 'none' : 'block';
      localStorage.setItem('currency', display_currency);
    }
  });

  $('h3[rel="toggle_afisha"]').click(function() {
    $('#afisha').slideToggle('slow');

    if( lsa ) {
      display_afisha = ( display_afisha == 'block' ) ? 'none' : 'block';
      localStorage.setItem('afisha', display_afisha);
    }
  });

  $('h3[rel="toggle_chat"]').click(function() {
    $('#chat').slideToggle('slow');

    if( lsa ) {
      display_chat = ( display_chat == 'block' ) ? 'none' : 'block';
      localStorage.setItem('chat', display_chat);
    }
	
	if( display_chat == 'block' ) {
		ajax.chat_message(2);
	} else {
		clearInterval(chat_refresh);
	}
  });

  var today_visitors_loaded = false;

  ajax.view_today_visitors = function() {
    ajax.exec({
      action: 'view_today_visitors'
    });
  };

  ajax.callback.view_today_visitors = function(data) {
    $('#users_today').append(data.html);
    today_visitors_loaded = true;
  };

  $('p[rel="toggle_users_today"]').click(function() {
    if( today_visitors_loaded == false ) {
      ajax.view_today_visitors();
    }

    $('#users_today').slideToggle('slow');
  });
  
	<!-- IF CHAT_ALLOWED -->
	<!-- IF SHOW_MODER_OPTIONS -->
	ajax.chat_ban = function(id) {
		ajax.exec({ action: 'chat_ban', id: id });
	};
	
	ajax.callback.chat_ban = function(response) { };
		
	ajax.chat_delete = function(id) {
		ajax.exec({ action: 'chat_delete', id: id });
	};
	
	ajax.callback.chat_delete = function(response) {
		if( response.html == 'OK' ) {
			$('.chat-comment[data-id=' + response.id + '] .chat-text').html('<i>сообщение скрыто</i>');
		}
	};
	<!-- ENDIF -->
	
	ajax.chat_message = function(mode) {
		ajax.exec({ action: 'chat_message', message: ((mode == 1) ? $('#message').val() : '') });
		clearInterval(chat_refresh);
		chat_refresh = setInterval(function() { ajax.chat_message(2); }, 30000);
	};

	ajax.callback.chat_message = function(response) {
		$('#chat_messages').show().html(response.html);
		
		$('.chat-text').each(function() {
			if( $(this).text().match('{USERNAME_ESCAPED}') ) {
				$(this).addClass('chat-mention');
			}
		});
		
	    if (!S_LOCAL) {
	      $('img.smile', '#chat_messages').each(function() { $(this).attr('src', $(this).attr('src').replace('static.local.ivacuum.ru', 'ivacuum.org').replace('0.ivacuum.org', 'ivacuum.org')); });
				$("a[href^='http://t.ivacuum.ru']", '#chat_messages').each(function() {
					$(this).attr('href', $(this).attr('href').replace('http://t.ivacuum.ru', 'http://t.internet.ivacuum.ru'));
				});
	    }
	};

	$('#chat_post').bind('click', function() {
		ajax.chat_message(1);
		$('#message').attr('value', '').focus();
		return false;
	});
	
	$('#chat_smilies').bind('click', function() {
		window.open('/posting.php?mode=smilies', '_phpbbsmilies', 'height=540, resizable=yes, scrollbars=yes ,width=620');
		return false;
	});
	
	$('.chat-nickbuffer').live('click', function() {
		$('#message').attr('value', $('.chat-nick', this).text() + ', ' + $('#message').val()).focus();
		return false;
	});
	
	<!-- IF SHOW_MODER_OPTIONS -->
	$('.chat-ban').live('click', function() {
		if( confirm('Действительно забанить участника?') ) {
			ajax.chat_ban($(this).data('id'));
		}
		
		return false;
	});
	
	$('.chat-delete').live('click', function() {
		if( confirm('Действительно скрыть сообщение?') ) {
			ajax.chat_delete($(this).data('id'));
		}
		
		return false;
	});
	
	$('.chat-comment').live('mouseenter', function() {
		$('.chat-ban, .chat-delete, .chat-profile', this).toggle();
	});
	
	$('.chat-comment').live('mouseleave', function() {
		$('.chat-ban, .chat-delete, .chat-profile', this).toggle();
	});
	<!-- ENDIF -->
		
	if( display_chat == 'block' ) {
		$('#message').width($('#chat').width() - 255);
		ajax.chat_message(2);
	}
	<!-- ENDIF -->
});
</script>