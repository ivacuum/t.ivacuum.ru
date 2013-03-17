<td id="sidebar1">
<div id="sidebar1-wrap">
	<h3><a href="feed.php?feed=lenta">Новости lenta.ru</a></h3>
	<p>Одно из ведущих российских новостных интернет-изданий</p>
	<br />
	<h3><a href="feed.php?feed=beeline_internet">Новости Билайн</a></h3>
	<p>Новости интернет услуг Билайн</p>
	<br />
	<h3><a href="feed.php?feed=beeline">Новости Билайн</a></h3>
	<p>Новости для абонентов сотовой связи Билайн</p>
	<br />
	<h3><a href="feed.php?feed=bash">bash.im</a></h3>
	<p>Смешные, забавные фрагменты электронных переписок или произошедшие истории</p>
	<br />
	<h3><a href="feed.php?feed=ithappens">ithappens.ru</a></h3>
	<p>Сборник историй из жизни системных администраторов, инженеров, эникейщиков и программистов</p>
	<br />
	<h3><a href="feed.php?feed=nefart">nefart.ru</a></h3>
	<p>Короткие комичные истории о том, как кому-то в чем-то не повезло или не подфартило</p>
	<br />
	<h3><a href="feed.php?feed=horoscope">Гороскоп</a></h3>
	<p>Ежедневный прогноз</p>
</div>
</td>
<td id="main_content"> 
<div id="main_content_wrap" class="feeds"> 

<h1>{FEED_TITLE}</h1>
<br />
<!-- BEGIN feed -->
<h3><img src="{STATIC_PATH}/i/_/<!-- IF feed.ICON -->{feed.ICON}<!-- ELSE -->newspaper<!-- ENDIF -->.png" alt="" style="vertical-align: text-top;"> {feed.TITLE}<!-- IF feed.LINK --> <a href="{feed.LINK}"><img src="{STATIC_PATH}/i/_/external.png" alt="" style="vertical-align: text-top;"></a><!-- ENDIF --></h3>
<!-- IF feed.TIME -->
<p><img src="{STATIC_PATH}/i/_/clock.png" alt="" style="vertical-align: text-top;"> <span class="med">{feed.TIME}</span></p>
<!-- ENDIF -->
<p class="feed-text">{feed.TEXT}</p>
<!-- END feed -->