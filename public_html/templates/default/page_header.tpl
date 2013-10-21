<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
{META}
<link rel="shortcut icon" href="{STATIC_PATH}/i/t/images/siteicon.png">
<!-- IF DEVELOPER2 -->
<link rel="stylesheet" href="{STATIC_PATH}/i/bootstrap/2.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="{STATIC_PATH}/i/bootstrap/2.3.1/css/expansion.css">
<script src="{STATIC_PATH}/i/bootstrap/2.3.1/js/bootstrap.min.js"></script>
<!-- ENDIF -->
<link rel="stylesheet" href="{STATIC_PATH}/js/jqueryui/1.10.0/themes/smoothness/minified/jquery-ui.min.css?v={$bb_cfg['css_ver']}">
<link rel="stylesheet" href="{STATIC_PATH}/i/t/css/style.css?v={$bb_cfg['css_ver']}">
<script>var S_LOCAL = <!-- IF STATIC_PATH eq '//0.ivacuum.org' -->true<!-- ELSE -->false<!-- ENDIF -->;</script>
<script src="{STATIC_PATH}/js/jquery/1.8.2/jquery.pack.js?v={$bb_cfg['js_ver']}"></script>
<!-- IF INCLUDE_BBCODE_JS -->
<script src="{STATIC_PATH}/js/hs.min.js"></script>
<link rel="stylesheet" href="{STATIC_PATH}/i/highslide/highslide.css">
<!-- ENDIF / INCLUDE_BBCODE_JS -->
<script src="{STATIC_PATH}/i/t/js/main.js?v={$bb_cfg['js_ver']}"></script>
<!-- IF INCLUDE_BBCODE_JS -->
<script src="{STATIC_PATH}/i/t/js/bbcode.js?v={$bb_cfg['js_ver']}"></script>
<script>
var postImg_MaxWidth = document.documentElement.clientWidth - {POST_IMG_WIDTH_DECR_JS} - 30;
var postImgAligned_MaxWidth = Math.round(document.documentElement.clientWidth / 3) + 50;
var attachImg_MaxWidth = document.documentElement.clientWidth - {ATTACH_IMG_WIDTH_DECR_JS};
var hidePostImg = false;

function copyText_writeLink(node) {
  if (!is_ie) return;
  document.write('<p style="float: right;"><a class="txtb" onclick="if (ie_copyTextToClipboard('+node+')) alert(\'{L_CODE_COPIED}\'); return false;" href="#">{L_CODE_COPY}</a></p>');
}

function initPostBBCode(context) {
  initExternalLinks(context);
  initPostImages(context);
  initSpoilers(context);

  if( typeof(hs) != 'undefined' && typeof(S_DISABLE_GALLERY) == 'undefined' ) {
    $("a[rel='highslide'] var img").each(function() {
      $(this).addClass('shadow-med');
    });
  }
}

function initPostImages(context) {
  if( hidePostImg ) return;
  var $in_spoilers = $('div.sp-body var.postImg', context);
  if( !S_LOCAL ) {
    $('img.smile').each(function() { $(this).attr('src', $(this).attr('src').replace('static.local.ivacuum.ru', 'ivacuum.org').replace('0.ivacuum.org', 'ivacuum.org')); });
  }
  
  $('var.postImg', context).not($in_spoilers).each(function() {
    var $v = $(this);
		
		if (S_LOCAL) {
			$v.attr('title', $v.attr('title').replace('//img.ivacuum.ru', '//img.local.ivacuum.ru').replace('//static.ivacuum.ru', '//0.ivacuum.org').replace('//ivacuum.org', '//0.ivacuum.org'));
		}
		
    var src = $v.attr('title');
    var $img = $('<img src="' + src + '" class="' + $v.attr('className') + '" alt="pic">');
    $img = fixPostImage($img);
    var maxW = ( $v.hasClass('postImgAligned') ) ? postImgAligned_MaxWidth : postImg_MaxWidth;
    $img.bind('click', function() { return imgFit(this, maxW); });
    if( user.opt_js.i_aft_l ) {
      $('#preload').append($img);
      var loading_icon = '<a href="' + src + '" target="_blank"><img src="{STATIC_PATH}/i/tracker/loading_1.gif" alt=""></a>';
      $v.html(loading_icon);
      if( $.browser.msie ) {
        $v.after('<wbr>');
      }
      $img.one('load', function() {
        imgFit(this, maxW);
        $v.empty().append(this);
      });
    } else {
      $img.one('load', function() {
        imgFit(this, maxW);
      });
      $v.empty().append($img);
      if( $.browser.msie ) {
        $v.after('<wbr>');
      }
    }
  });
}

function initSpoilers(context) {
  $('div.sp-body', context).each(function() {
    var $sp_body = $(this);
    var name = this.title || 'скрытый текст';
    this.title = '';
    var $sp_head = $('<div class="sp-head folded clickable"></div>');
    $sp_head.text(name).insertBefore($sp_body).click(function(e) {
      if (!$sp_body.hasClass('inited')) {
        initPostImages($sp_body);
        var $sp_fold_btn = $('<div class="sp-fold clickable">[свернуть]</div>').click(function(){
          $.scrollTo($sp_head, { duration: 200, axis: 'y', offset: -200 });
          $sp_head.click().animate({ opacity: 0.1 }, 500).animate({ opacity: 1 }, 700);
        });
        $sp_body.prepend('<div class="clear"></div>').append('<div class="clear"></div>').append($sp_fold_btn).addClass('inited');
      }
      if( e.shiftKey ) {
        e.stopPropagation();
        e.shiftKey = false;
        var fold = $(this).hasClass('unfolded');
        $('div.sp-head', $($sp_body.parents('td')[0])).filter(function() { return $(this).hasClass('unfolded') ? fold : !fold }).click();
      } else {
        $(this).toggleClass('unfolded');
        $sp_body.slideToggle('fast');
      }
    });
  });
}

function fixPostImage($img) {
  var allowed_image_hosts = /ivacuum.ru|ivacuum.org/i;
  var src = $img[0].src;

  if( !(src.match(allowed_image_hosts)) ) {
    $img.attr({ src: "{STATIC_PATH}/i/tracker/smilies/tr_oops.gif", title: "Прочтите правила выкладывания скриншотов!" });
  }

  return $img;
}

function initExternalLinks(context) {
  $("a.postLink:not([href*='"+ window.location.hostname +"/'])", context).attr({ target: '_blank' });
}

$(document).ready(function() {
  $('div.post_body, div.signature').each(function() { initPostBBCode( $(this) ) });

  if (S_LOCAL) {
	  $("img[src^='http://static.ivacuum.ru/'], img[src^='//static.ivacuum.ru/']").each(function() {
		  $(this).attr('src', $(this).attr('src').replace('//static.ivacuum.ru', '//0.ivacuum.org'));
	  });

	  $("img[src^='http://ivacuum.org/'], img[src^='//ivacuum.org/']").each(function() {
		  $(this).attr('src', $(this).attr('src').replace('//ivacuum.org', '//0.ivacuum.org'));
	  });

	  $("a[href^='http://t.internet.ivacuum.ru']").each(function() {
		  $(this).attr('href', $(this).attr('href').replace('http://t.internet.ivacuum.ru', 'http://t.ivacuum.ru'));
	  })
  } else {
	  $("img[src^='//0.ivacuum.org'], img[src^='http://t.ivacuum.ru']").each(function() {
		  $(this).attr('src', $(this).attr('src').replace('//0.ivacuum.org', '//ivacuum.org').replace('http://t.ivacuum.ru', 'http://t.internet.ivacuum.ru'));
	  });
	  
	  $("a[href^='http://t.ivacuum.ru']").each(function() {
		  $(this).attr('href', $(this).attr('href').replace('http://t.ivacuum.ru', 'http://t.internet.ivacuum.ru'));
	  })
  }
});
</script>
<!-- ENDIF / INCLUDE_BBCODE_JS -->

<script>
var cookieDomain  = "{$bb_cfg['cookie_domain']}";
var cookiePath    = "{$bb_cfg['cookie_path']}";
var cookieSecure  = {$bb_cfg['cookie_secure']};
var cookiePrefix  = "{$bb_cfg['cookie_prefix']}";
var LOGGED_IN     = {LOGGED_IN};
var InfoWinParams = 'HEIGHT=510,resizable=yes,WIDTH=780';

var user = {
	opt_js: {USER_OPTIONS_JS},

	set: function(opt, val, days, reload) {
		this.opt_js[opt] = val;
		setCookie('opt_js', $.toJSON(this.opt_js), days);
		if (reload) {
			window.location.reload();
		}
	}
}

<!-- IF SHOW_JUMPBOX -->
$(document).ready(function(){
	$("div.jumpbox").html('\
		<span id="jumpbox-container"> \
		<select id="jumpbox"> \
			<option id="jumpbox-title" value="-1">&nbsp;&raquo;&raquo; {L_JUMPBOX_TITLE} &nbsp;</option> \
		</select> \
		</span> \
		<input id="jumpbox-submit" type="button" class="btn btn-small" value="{L_GO}"> \
	');
	$('#jumpbox-container').one('click', function(){
		$('#jumpbox-title').html('&nbsp;&nbsp; {L_LOADING} ... &nbsp;');
		var jumpbox_src = '/ajax/html/' + ({LOGGED_IN} ? 'jumpbox_user.html' : 'jumpbox_guest.html');
		$(this).load(jumpbox_src);
		$('#jumpbox-submit').click(function(){ window.location.href='{FORUM_URL}'+$('#jumpbox').val(); });
	});
});
<!-- ENDIF -->

function getElText(e) {
	var t = '';
	if (e.textContent !== undefined) {
		t = e.textContent;
	} else if (e.innerText !== undefined) {
		t = e.innerText;
	} else {
		t = jQuery(e).text();
	}
	return t;
}
function escHTML(txt) {
	return txt.replace(/</g, '&lt;');
}
function cfm(txt) {
	return window.confirm(txt);
}

<!-- IF HTML_WBR_TAG != '<wbr>' -->
$(document).ready(function() {
	$('div.post_body wbr').after('{HTML_WBR_TAG}');
});
<!-- ENDIF -->

var ajax = new Ajax('{AJAX_HANDLER}', 'POST', 'json');
</script>
<!--[if IE 8]><style>.sf_title { white-space: normal; }</style><![endif]-->
<!--[if gte IE 7]><style>input[type="checkbox"] { margin-bottom: -1px; }</style><![endif]-->
<!--[if IE]><style>.code-copy { display: block; } .post-hr { margin: 2px auto; }</style><![endif]-->
<style>.menu-sub, #ajax-loading, #ajax-error, var.ajax-params { display: none; }</style>
<title><!-- IF PAGE_TITLE -->{PAGE_TITLE} :: {SITENAME}<!-- ELSE -->{SITENAME}<!-- ENDIF --></title>
</head>
<body>
<!-- IF EDITABLE_TPLS -->
<div id="editable-tpl-input" style="display: none;">
	<span class="editable-inputs nowrap" style="display: none;">
		<input type="text" class="editable-value">
		<input type="button" class="editable-submit" value="&raquo;" style="width: 30px; font-weight: bold;">
		<input type="button" class="editable-cancel" value="x" style="width: 30px;">
	</span>
</div>
<div id="editable-tpl-yesno-select" style="display: none;">
	<span class="editable-inputs nowrap" style="display: none;">
		<select class="editable-value"><option value="1">{L_YES}</option><option value="0">{L_NO}</option></select>
		<input type="button" class="editable-submit" value="&raquo;" style="width: 30px; font-weight: bold;">
		<input type="button" class="editable-cancel" value="x" style="width: 30px;">
	</span>
</div>
<div id="editable-tpl-yesno-radio" style="display: none;">
	<span class="editable-inputs nowrap" style="display: none;">
		<label><input class="editable-value" type="radio" name="editable-value" value="1">{L_YES}</label>
		<label><input class="editable-value" type="radio" name="editable-value" value="0">{L_NO}</label>&nbsp;
		<input type="button" class="editable-submit" value="&raquo;" style="width: 30px; font-weight: bold;">
		<input type="button" class="editable-cancel" value="x" style="width: 30px;">
	</span>
</div>
<!-- ENDIF / EDITABLE_TPLS -->

<div id="ajax-loading"><b>Загрузка...</b></div>
<div id="ajax-error"><b>Ошибка</b></div>
<div id="preload" style="position: absolute; overflow: hidden; top: 0; left: 0; height: 1px; width: 1px;"></div>

<div id="body_container">

<!--************************************************************************-->
<!-- IF SIMPLE_HEADER -->
<!--========================================================================-->

<style type="text/css">
body { background: #E3E3E3; min-width: 10px; }
</style>

<!--========================================================================-->
<!-- ELSEIF IN_ADMIN -->
<!--========================================================================-->

<!--========================================================================-->
<!-- ELSE -->
<!--========================================================================-->

<!--page_container-->
<div id="page_container">
<a name="top"></a>

<!--page_header-->
<div id="page_header">

<!--main_nav-->
<div class="top-bar shadow-light<!-- IF HAVE_NEW_PM or HAVE_UNREAD_PM --> new-pm<!-- ENDIF -->" id="main-nav">
	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<a href="{U_INDEX}"><img src="{STATIC_PATH}/i/_/home.png" alt=""> {L_HOME}</a>
			<a href="{U_TRACKER}"><img src="{STATIC_PATH}/i/_/table.png" alt=""> {L_TRACKER}</a>
			<a href="{U_UPLOAD_IMAGE}"><img src="{STATIC_PATH}/i/_/image_plus.png" alt=""> Загрузка</a>
			<a href="{U_SEARCH}"><img src="{STATIC_PATH}/i/_/magnifier.png" alt=""> {L_SEARCH}</a>
			<a href="viewforum.php?f=4"><img src="{STATIC_PATH}/i/_/question_balloon.png" alt=""> Помощь</a>
			<a href="{U_GROUP_CP}"><img src="{STATIC_PATH}/i/_/users.png" alt=""> {L_USERGROUPS}</a>
			<a href="{U_MEMBERLIST}"><img src="{STATIC_PATH}/i/_/cards_address.png" alt=""> {L_MEMBERLIST}</a>
			<a href="feed.php"><img src="{STATIC_PATH}/i/_/feed.png" alt=""> Ленты</a>
		</td>
		<!-- IF LOGGED_IN -->
		<td class="tRight">
			<a href="{U_READ_PM}"<!-- IF HAVE_NEW_PM or HAVE_UNREAD_PM --> class="new-pm-link"<!-- ENDIF -->><img src="{STATIC_PATH}/i/_/mail_open<!-- IF HAVE_NEW_PM or HAVE_UNREAD_PM -->_document<!-- ENDIF -->.png" alt="{PM_INFO}" title="{PM_INFO}"> {L_PRIVATE_MESSAGES} ({PM_INFO})</a>
		</td>
		<!-- ENDIF -->
	</tr>
	</table>
</div>
<!--/main_nav-->

<div id="logo" style="float: left;"><a href="{U_INDEX}" title="Перейти на главную страницу"><img src="{STATIC_PATH}/i/t/images/logo.png" alt="" width="690" height="100"><!--<img src="{STATIC_PATH}/i/t/images/logo_snow.png" alt="" width="689" height="100">--></a></div>
<div style="float: right; margin-right: 0.3em;"><a href="https://lk.beeline.ru/"><img src="{STATIC_PATH}/i/t/images/beeline.png" alt="Трекер расположен в локальной сети Билайн-Калуга" title="Трекер расположен в локальной сети Билайн-Калуга" width="128" height="100"></a></div>

<br clear="all">

<!-- IF LOGGED_IN -->
<!--logout-->
<div class="topmenu">
	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td width="45%">
			Вы зашли как: <b class="med">{THIS_USERNAME}</b> &nbsp;<a class="btn btn-negative btn-icons" href="{U_LOGIN_LOGOUT}" onclick="return confirm('Вы уверены, что хотите выйти?');"><img src="{STATIC_PATH}/i/_/lock.png" alt="">{L_LOGOUT}</a>
		</td>
		<td style="pading: 2px;">
			<div>
			<form id="quick-search" method="post" action="">
			<input type="hidden" name="max" value="1">
			<input type="hidden" name="to" value="1">
			<img src="{STATIC_PATH}/i/_/magnifier.png" alt="{L_SEARCH}" title="{L_SEARCH}" style="vertical-align: text-top;">
			<!-- IF SEARCH_TEXT -->
			<input id="search-text" type="text" name="nm" value="{SEARCH_TEXT}" tabindex="1" style="width: 200px;">
			<!-- ELSE -->
			<input id="search-text" type="text" name="nm" value="поиск..." tabindex="1" class="hint" style="width: 200px;">
			<!-- ENDIF -->
			<select id="search-action">
				<option value="tracker.php" selected="selected"> раздачи </option>
				<option value="search.php"> все темы </option>
			</select>
			<input type="submit" class="btn btn-small btn-primary" value="&raquo;" style="width: 30px;">
			</form>
			</div>
		</td>
		<td width="55%" class="tRight">
			<a class="btn btn-left btn-icons" href="{U_OPTIONS}"><img src="{STATIC_PATH}/i/_/wrench.png" alt="">{L_OPTIONS}</a><a class="btn btn-middle btn-icons" href="{U_CUR_DOWNLOADS}"><img src="{STATIC_PATH}/i/_/card_address.png" alt="">{L_PROFILE}</a><a class="btn btn-right btn-icons menu-root menu-alt1" href="#dls-menu"><img src="{STATIC_PATH}/i/_/drive_network.png" alt="">Закачки</a>
		</td>
	</tr>
	</table>
</div>
<!--/logout-->
<style type="text/css">
.menu-a { background: #FFFFFF; border: 1px solid #92A3A4; }
.menu-a a { background: #EFEFEF; padding: 4px 10px 5px; margin: 1px; display: block; }
</style>
<div class="menu-sub" id="dls-menu">
	<div class="menu-a bold nowrap">
		<a class="med" href="{U_TRACKER}?rid={SESSION_USER_ID}#results">{L_CUR_UPLOADS}</a>
		<a class="med" href="{U_CUR_DOWNLOADS}">{L_CUR_DOWNLOADS}</a>
		<a class="med" href="{U_SEARCH}?dlu={SESSION_USER_ID}&amp;dlc=1">{L_SEARCH_DL_COMPLETE_DOWNLOADS}</a>
		<a class="med" href="{U_SEARCH}?dlu={SESSION_USER_ID}&amp;dlw=1">{L_SEARCH_DL_WILL_DOWNLOADS}</a>
	</div>
</div>
<!-- ELSE -->

<div class="topmenu">
<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tCenter pad_2">
			<a class="btn btn-orange btn-active" href="{U_REGISTER}" id="register_link"><b>{L_REGISTER}</b></a> &nbsp;
			<form action="{S_LOGIN_ACTION}" method="post">
			{L_USERNAME}: <input type="text" name="login_username" size="12" tabindex="1" accesskey="l">
			{L_PASSWORD}: <input type="password" name="login_password" size="12" tabindex="2">
			<label title="{L_AUTO_LOGIN}"><input type="checkbox" name="autologin" value="1" tabindex="3"> {L_REMEMBER}</label>&nbsp;
			<input class="btn btn-small" type="submit" name="login" value="{L_LOGIN}" tabindex="4">
			</form>
			&nbsp;&#0183;&nbsp;<a href="{U_SEND_PASSWORD}">{L_FORGOTTEN_PASSWORD}</a>
		</td>
	</tr>
</table>
</div>
<!-- ENDIF -->

</div>
<!--/page_header-->

<!--menus-->

<!-- IF SHOW_ONLY_NEW_MENU -->
<div class="menu-sub" id="only-new-options">
	<table cellspacing="1" cellpadding="4">
	<tr>
		<th>{L_DISPLAYING_OPTIONS}</th>
	</tr>
	<tr>
		<td>
			<fieldset id="show-only">
			<legend>{L_SHOW_ONLY}</legend>
			<div class="pad_4">
				<label>
					<input id="only_new_posts" type="checkbox" <!-- IF ONLY_NEW_POSTS_ON -->{CHECKED}<!-- ENDIF -->
						onclick="
							user.set('only_new', ( this.checked ? {ONLY_NEW_POSTS} : 0 ), 365, true);
							$('#only_new_topics').attr('checked', false);
						">{L_ONLY_NEW_POSTS}
				</label>
				<label>
					<input id="only_new_topics" type="checkbox" <!-- IF ONLY_NEW_TOPICS_ON -->{CHECKED}<!-- ENDIF -->
						onclick="
							user.set('only_new', ( this.checked ? {ONLY_NEW_TOPICS} : 0 ), 365, true);
							$('#only_new_posts').attr('checked', false);
						">{L_ONLY_NEW_TOPICS}
				</label>
			</div>
			</fieldset>
		</td>
	</tr>
	</table>
</div><!--/only-new-options-->
<!-- ENDIF / SHOW_ONLY_NEW_MENU -->

<!--/menus-->



<!--page_content-->
<div id="page_content">
<table cellspacing="0" cellpadding="0" border="0" style="width: 100%;"><tr>

<!-- IF SHOW_SIDEBAR1 -->
	<!--sidebar1-->
<script type="text/javascript">
var display_forecast = 'none';
</script>
	<td id="sidebar1">
	<div id="sidebar1-wrap">

<!-- IF SHOW_BT_USERDATA -->
<div id="user_ratio">

<h3 rel="toggle_ratio" style="cursor: pointer;">{L_BT_RATIO}</h3>
<div id="ratio">
<table cellpadding="0">
<tr><td>{L_YOUR_RATIO}</td><td><!-- IF DOWN_TOTAL_BYTES gt MIN_DL_BYTES --><b>{USER_RATIO}</b><!-- ELSE --><b>нет</b> (DL < {MIN_DL_FOR_RATIO})<!-- ENDIF --></td></tr>
<tr><td>{L_DOWNLOADED}</td><td class="leechmed"><b>{DOWN_TOTAL}</b></td></tr>
<tr><td>{L_UPLOADED}</td><td class="seedmed"><b>{UP_TOTAL}</b></td></tr>
<tr><td><i>{L_RELEASED}</i></td><td class="seedmed">{RELEASED}</td></tr>
<tr><td><i>{L_BT_BONUS_UP}</i></td><td class="seedmed">{UP_BONUS}</td></tr>
<tr><td>таймбонусы</td><td class="seedmed">{TIMEBONUS}</td></tr>
<tr><td>торренты</td><td class="med"><img src="{STATIC_PATH}/i/tracker/icon_up.gif" alt="" style="vertical-align: text-top;"> {USER_SEEDING} &nbsp;<img src="{STATIC_PATH}/i/tracker/icon_down.gif" alt="" style="vertical-align: text-top;"> {USER_LEECHING}</td></tr>
</table>
</div>
</div>
<!-- ENDIF -->

<h3 rel="toggle_feedback" style="cursor: pointer;">Обратная связь</h3>
<div id="feedback" class="med" style="line-height: 175%;">
	<img src="{STATIC_PATH}/i/_/question_balloon.png" alt="" style="vertical-align: text-top;"> <a href="mailto:support@t.ivacuum.ru" title="Написать письмо"><b>Задать вопрос</b></a><br>
	<img src="{STATIC_PATH}/i/_/light.png" alt="" style="vertical-align: text-top;"> <a href="mailto:dev@t.ivacuum.ru" title="Написать письмо"><b>Предложения</b></a><br>
	<img src="{STATIC_PATH}/i/_/bug.png" alt="" style="vertical-align: text-top;"> <a href="mailto:bugs@t.ivacuum.ru" title="Написать письмо"><b>Сообщить об ошибке</b></a>
</div>

<h3 rel="toggle_forecast" style="cursor: pointer;">Прогноз погоды</h3>
<div id="forecast" style="display: none;">
<!-- BEGIN forecast -->
<p><span class="med">
	<b>{forecast.DAY} {forecast.MONTH}, {forecast.TOD}</b><br>
	<img src="{STATIC_PATH}/i/_/{forecast.ICON}.png" alt="" title="{forecast.CLOUDINESS}, {forecast.PRECIPITATION}" style="vertical-align: text-top;"> <span style="background-color: #{forecast.TCOLOR};">{forecast.TMIN}..{forecast.TMAX}&deg;</span><br>
	<img src="{STATIC_PATH}/i/_/arrow_{forecast.WINDDEG}_medium.png" alt="" title="{forecast.WINDDIR}" style="vertical-align: text-top;"> {forecast.WIND} м/с, влажность {forecast.WET}%
</span></p>
<!-- IF forecast.S_ROW_COUNT ne ( forecast.S_NUM_ROWS - 1 ) --><hr class="dashed" style="width: 80%;"><!-- ENDIF -->
<!-- END forecast -->
</div>

<h3 rel="toggle_currency" style="cursor: pointer;">Курс валют</h3>
<div id="currency" style="display: none;">
<p><span class="med">
	<img src="{STATIC_PATH}/i/_/currency.png" alt="" title="Доллар США" style="vertical-align: text-top;"> {USD} руб.<br>
	<img src="{STATIC_PATH}/i/_/currency_euro.png" alt="" title="Евро" style="vertical-align: text-top;"> {EURO} руб.<br>
	<img src="{STATIC_PATH}/i/_/currency_pound.png" alt="" title="Фунт стерлингов" style="vertical-align: text-top;"> {POUND} руб.
</span></p>
</div>

<h3 rel="toggle_afisha" style="cursor: pointer;">Афиша кинотеатров</h3>
<div class="med" id="afisha" style="display: none;">
<!-- IF AFISHA_AVAILABLE -->
<!-- BEGIN afisha -->
<p>
	<!-- IF afisha.FORMAT eq 1 -->
	<img src="{STATIC_PATH}/i/_/spectacle_3d.png" alt="" title="В формате 3D" style="vertical-align: text-top;"> [3D]
	<!-- ELSE -->
	<img src="{STATIC_PATH}/i/_/spectacle_sunglass.png" alt="" style="vertical-align: text-top;">
	<!-- ENDIF -->
	<a href="{afisha.U_SEARCH}"><b>{afisha.TITLE}</b></a><br>
	<img src="{STATIC_PATH}/i/_/clock_select.png" alt="" title="Сеансы" style="vertical-align: text-top;"> {afisha.SESSIONS}<br>
</p>
<hr class="dashed" style="width: 98%;">
<!-- END afisha -->
<p><b>Расписание актуально:</b></p>
<p><img src="{STATIC_PATH}/i/_/calendar_select_week.png" alt="" style="vertical-align: text-top;"> {AFISHA_DATE}</p><br>
<p><b>Бронирование билетов:</b></p>
<p><img src="{STATIC_PATH}/i/_/telephone.png" alt="" style="vertical-align: text-top;"> 22-28-22, 74-90-70</p>
<!-- ELSE -->
<!-- <p>Афиша временно недоступна</p> -->
<ul>
	<li><a href="http://cinema-starkaluga.ru/afisha/">Синема-стар</a></li>
	<li><a href="http://www.cinemastar.ru/cinemas/5/92/">Синема-стар РИО</a></li>
	<li><a href="http://arlekino40.ru/">Арлекино</a></li>
</ul>
<!-- ENDIF -->
</div>

<!-- IF TOP_RELEASERS -->
<h3 rel="toggle_top_releasers" style="cursor: pointer;">Топ релизеров</h3>
<table cellpadding="2" cellspacing="0" id="top_releasers" style="display: none;" width="100%">
</table>
<!-- ENDIF -->
<h3><a href="memberlist.php?mode=uploaded&order=DESC">Топ сидеров</a></h3>
<h3><a href="memberlist.php?mode=downloaded&order=DESC">Топ личеров</a></h3>
<!-- IF TOP_SHARE -->
<h3 rel="toggle_top_share" style="cursor: pointer;">Топ шар</h3>
<table cellpadding="2" cellspacing="0" id="top_share" style="display: none;" width="100%">
</table>
<!-- ENDIF -->

<!-- IF LAST_ADDED_ON -->
<h3>Новые раздачи</h3>
<!-- BEGIN t_last_added -->
<span class="med"><img src="{STATIC_PATH}/i/_/{t_last_added.IMAGE}.png" alt="" style="vertical-align: text-top;"> <a href="viewtopic.php?t={t_last_added.TOPIC_ID}" class="med" title="{t_last_added.TITLE}">{t_last_added.SHORT_TITLE}</a><br><img src="{STATIC_PATH}/i/_/folder_open.png" alt="" style="vertical-align: text-top;"> <a href="viewforum.php?f={t_last_added.FORUM_ID}">{t_last_added.FORUM}</a><br><img src="{STATIC_PATH}/i/_/card_address.png" alt="Автор" title="Автор" style="vertical-align: text-top;"> <a href="profile.php?mode=viewprofile&u={t_last_added.POSTER_ID}">{t_last_added.POSTER}</a><br><img src="{STATIC_PATH}/i/_/clock_select.png" alt="Релиз" title="Релиз" style="vertical-align: text-top;"> {t_last_added.TIME} назад<br><img src="{STATIC_PATH}/i/tracker/icon_up.gif" alt="Сиды" title="Сиды" style="vertical-align: text-top;"> {t_last_added.SEEDERS} &nbsp;<img src="{STATIC_PATH}/i/tracker/icon_down.gif" alt="Личи" title="Личи" style="vertical-align: text-top;"> {t_last_added.LEECHERS}<!-- IF t_last_added.SPEED gt 0 --> &nbsp;({t_last_added.SPEED})<!-- ENDIF --><!-- IF LOGGED_IN --> &nbsp;<a href="{t_last_added.U_DOWNLOAD}"><img src="{STATIC_PATH}/i/_/drive_download.png" alt="" style="vertical-align: text-top;"></a><!-- ENDIF --></span>
<!-- IF t_last_added.S_ROW_COUNT ne ( t_last_added.S_NUM_ROWS - 1 ) --><hr class="dashed" style="width: 99%;"><!-- ENDIF -->
<!-- END t_last_added -->
<!-- ENDIF -->

<h3>BitTorrent клиенты</h3>
<span class="med"><img src="{STATIC_PATH}/i/_/windows.png" alt="" style="vertical-align: text-top;"> <a href="//dl.local.ivacuum.ru/150/">&micro;Torrent 3.0</a><br>
<img src="{STATIC_PATH}/i/_/linux.png" alt="" style="vertical-align: text-top;"> <a href="//dl.local.ivacuum.ru/152/">ktorrent 4.1.2</a><br>
<img src="{STATIC_PATH}/i/_/macosx.png" alt="" style="vertical-align: text-top;"> <a href="//dl.local.ivacuum.ru/151/">Transmission 2.41</a>
</span><br>

		<?php if (!empty($bb_cfg['sidebar1_static_content_path'])) include $bb_cfg['sidebar1_static_content_path']; ?>

		<img width="210" class="spacer" src="{SPACER}" alt="">

	</div><!--/sidebar1-wrap-->
	</td><!--/sidebar1-->
<!-- ENDIF -->

<!--main_content-->
<td id="main_content">
<div id="main_content_wrap">
<!-- IF SHOW_LATEST_NEWS -->
<!--latest_news-->
<div id="latest_news">
<table cellspacing="0" cellpadding="0" width="100%">
<tr>
<td width="70%">
<h3>Последние объявления</h3>
<table cellpadding="0">
<!-- BEGIN news -->
<tr>
<td><div class="news_date">{news.NEWS_TIME}</div></td>
<td width="100%"><div class="news_title<!-- IF news.NEWS_IS_NEW --> new<!-- ENDIF -->"><a href="{TOPIC_URL}{news.NEWS_TOPIC_ID}">{news.NEWS_TITLE}</a></div></td>
</tr>
<!-- END news -->
</table></td></tr></table></div><!--/latest_news-->
<br style="clear: both;">
<!-- ENDIF / SHOW_LATEST_NEWS -->
<!-- ENDIF / COMMON_HEADER -->

<!-- IF ERROR_MESSAGE -->
<div class="info_msg_wrap">
<table class="error">
	<tr><td><div class="msg">{ERROR_MESSAGE}</div></td></tr>
</table>
</div>
<!-- ENDIF / ERROR_MESSAGE -->

<!-- IF INFO_MESSAGE -->
<div class="info_msg_wrap">
<table class="info_msg">
	<tr><td><div class="msg">{INFO_MESSAGE}</div></td></tr>
</table>
</div>
<!-- ENDIF / INFO_MESSAGE -->

<!-- page_header.tpl END -->
<!-- module_xx.tpl START -->