<?php require $GLOBALS['bb_cfg']['topic_tpl']['header']; ?>

<h1 class="maintitle"><a href="{U_VIEW_FORUM}">{FORUM_NAME}</a></h1>

<div class="nav">
	<p class="floatL"><a href="{U_INDEX}">{T_INDEX}</a></p>
	<!-- IF REGULAR_TOPIC_BUTTON --><p class="floatR"><a href="{REGULAR_TOPIC_HREF}">{L_POST_REGULAR_TOPIC}</a></p><!-- ENDIF -->
	<div class="clear"></div>
</div>

<?php require $GLOBALS['bb_cfg']['topic_tpl']['shared_header']; ?>

<div style="display: none;">
<form id="tpl-post-form" method="post" action="{S_ACTION}" name="post" class="tokenized">
	<input type="hidden" name="tor_required" value="1">
	<input type="hidden" name="preview" value="1">
	<input id="tpl-post-subject" type="text" name="subject" size="90" value="" />
	<textarea id="tpl-post-message" name="message" rows="1" cols="1"></textarea>
</form>
</div>

<div id="rel-form" style="display: none;">
<table class="forumline">
<col class="row1" width="20%">
<col class="row2" width="80%">
<thead>
<tr>
	<th colspan="2">Заполните форму для релиза</th>
</tr>
</thead>
<tbody id="rel-tpl"></tbody>
<tfoot>
<tr>
	<td colspan="2" class="pad_8 tCenter bold">На следующей странице проверьте оформление и загрузите torrent файл</td>
</tr>
<tr>
	<td class="catBottom" colspan="2">
		<input type="button" style="width: 200px;" value="Создать обычную тему" onclick="window.location = '{S_ACTION}'; return false;" />
		&nbsp;
		<input type="button" value="Продолжить" class="bold" style="width: 150px;" onclick="tpl_submit(true);" />
	</td>
</tr>
</tfoot>
</table>
</div>

<?php require $GLOBALS['bb_cfg']['topic_tpl']['shared_footer']; ?>

<div style="display: none;">
	<!-- исходные значения всех #tpl-src -->
	<textarea id="tpl-src-form-val" rows="10" cols="10">&lt;-title_rus INP[title_rus]   `на русском` -&gt;
&lt;-title_eng INP[title_eng]   `латиницей` -&gt;
&lt;-{Другие варианты названия} INP[{Другие варианты названия},90,80] `варианты разделять символом &quot;/&quot;` -&gt;

&lt;-poster                     INP[poster] `URL` E[load_pic_btn] -&gt;

&lt;-country                    INP[country,,40] -&gt;
&lt;-year                       INP[year,4,5] -&gt;
&lt;-genre                      INP[genre] -&gt;
&lt;-{Тип}                      SEL[anime_type] -&gt;
&lt;-{Эпизоды}                  INP[{Эпизоды},90,40] `например, &quot;серия 11 из 51&quot;, &quot;2-6 из 13&quot; и т.п.`] -&gt;
&lt;-playtime                   INP[playtime,,40] `например, &quot;24 эп, ~25 мин. серия&quot; или &quot;104 мин.&quot; (для фильмов)`  -&gt;
&lt;-anime_release_type         SEL[anime_release_type] -&gt;
&lt;-director                   INP[director,,40] -&gt;
&lt;-studio                     INP[studio,,40] -&gt;

&lt;-description                TXT[description] -&gt;


&lt;-moreinfo                   TXT[moreinfo] -&gt;
&lt;-{Тех. параметры}           SEL[{Качество}] `Релиз/Автор рипа (если известно)` INP[{Релиз/Автор рипа},,20] SEL[video_format] `BR` E[videofile_info_faq_url] -&gt;
&lt;-video                      INP[video] `- кодек, разрешение, битрейт (kbps), частота кадров (fps)` -&gt;
&lt;-{Аудио}                    INP[{Аудио}]`- кодек, битрейт (kbps), частота (Hz), количество каналов (ch)` `BR` `Первой дорожкой следует описывать русскую, если таковая есть` `BR` SEL[lang_anime] `Озвучка:` INP[{; Озвучка:},,40] `- тип (одноголосая/многоголосая/дубляж), авторы/студия` -&gt;
&lt;-{Аудио 2}                  INP[{Аудио 2}]`- кодек, битрейт (kbps), частота (Hz), количество каналов (ch)` `BR` SEL[lang_anime_2] `Озвучка:` INP[{; Озвучка 2:},,40] `- тип (одноголосая/многоголосая/дубляж), авторы/студия` -&gt;
&lt;-{Аудио 3}                  INP[{Аудио 3}]`- кодек, битрейт (kbps), частота (Hz), количество каналов (ch)` `BR` SEL[lang_anime_3] `Озвучка:` INP[{; Озвучка 3:},,40] `- тип (одноголосая/многоголосая/дубляж), авторы/студия` -&gt;
&lt;-{Субтитры}                 INP[{Субтитры}] `- Формат, внешние/встроенные/хардсаб` `BR` SEL[sub_all_anime] `Перевод:` INP[{; Перевод:},,40] `- авторы перевода (если известны)` -&gt;
&lt;-{Субтитры 2}               INP[{Субтитры 2}] `- Формат, внешние/встроенные/хардсаб` `BR` SEL[sub_all_anime_2] `Перевод:` INP[{; Перевод 2:},,40] `- авторы перевода (если известны)` -&gt;
&lt;-{Субтитры 3}               INP[{Субтитры 3}] `- Формат, внешние/встроенные/хардсаб` `BR` SEL[sub_all_anime_3] `Перевод:` INP[{; Перевод 3:},,40] `- авторы перевода (если известны)` -&gt;

&lt;-{Подробные тех. данные}    `Вставить информацию из отчета программы MediaInfo / AviInfo / avdump` E[videofile_info_faq_url] TXT[{Подробные тех. данные}] -&gt;
&lt;-{Список эпизодов}          TXT[{Список эпизодов},3] -&gt;

&lt;-{Отличия от существующих}  E[comparison_anime] TXT[{Отличия},5] -&gt;
&lt;-screenshots                TXT[screenshots] `BR` E[load_pic_btn] E[make_screenlist_faq_url] E[load_pic_faq_url] -&gt;</textarea>
	<textarea id="tpl-src-title-val" rows="10" cols="10">&lt;-title_rus title_eng {Другие варианты названия}-&gt;/ &lt;-director-&gt;(/) &lt;-anime_type-&gt;[,] &lt;-{Эпизоды}-&gt;[,] &lt;-anime_release_type_abr-&gt;[,] &lt;-lang_anime_abr lang_anime_2_abr lang_anime_3_abr sub_all_anime_abr sub_all_anime_2_abr sub_all_anime_3_abr-&gt;[,] &lt;-year genre {Качество}-&gt;[,]</textarea>
	<textarea id="tpl-src-msg-val" rows="10" cols="10">title_rus[HEAD,req]
title_eng[HEAD,req]
{Другие варианты названия}[HEAD,BR]
poster[img,POSTER,req]
country[req]
year[req,num]
genre[req]
anime_type[req]
{Эпизоды}[headonly]
playtime[req]
anime_release_type[req,BR]
director[req]
studio[BR]
description[req,BR]
moreinfo[BR]
{Качество}[req]
{Релиз/Автор рипа}[brackets]
video_format[req]
video[req]
{Аудио}[req]
lang_anime[req,inlineflag]
{; Озвучка:}[inline]
{Аудио 2}[]
lang_anime_2[inlineflag]
{; Озвучка 2:}[inline]
{Аудио 3}[]
lang_anime_3[inlineflag]
{; Озвучка 3:}[inline]
{Субтитры}[]
sub_all_anime[inline]
{; Перевод:}[inline]
{Субтитры 2}[]
sub_all_anime_2[inline]
{; Перевод 2:}[inline]
{Субтитры 3}[]
sub_all_anime_3[inline]
{; Перевод 3:}[inline]
{Подробные тех. данные}[pre]
{Список эпизодов}[spoiler,BR]
{Отличия}[spoiler]
screenshots[req,spoiler]</textarea>
	<textarea id="tpl-src-sel-val" rows="10" cols="10">{Качество}[DVDRip,TVRip,DTVRip,LDRip,WEBRip,HDTVRip,BDRip,PSNRip,VHSRip,CamRip]</textarea>
</div>

<noscript><div class="warningBox2 bold tCenter">Для показа необходимo включить JavaScript</div></noscript>