<table class="forumline">
<col class="row1" width="20%">
<col class="row2" width="80%">
<tr>
	<th colspan="2">{L_RELEASE_WELCOME}</th>
</tr>
<tr>
	<td><b><!-- IF TITLE_HREF --><a href="{TITLE_HREF}" target="_blank">{L_TITLE}</a><!-- ELSE -->{L_TITLE}<!-- ENDIF --></b>:</td>
	<td><input type="text" name="msg[release_name]" maxlength="90" size="80" /> <span class="med nowrap">{L_TITLE_DESC}</span></td>
</tr>
<tr>
	<td><b><!-- IF ORIGINAL_TITLE_HREF --><a href="{ORIGINAL_TITLE_HREF}" target="_blank">{L_ORIGINAL_TITLE}</a><!-- ELSE -->{L_ORIGINAL_TITLE}<!-- ENDIF --></b>:</td>
	<td><input type="text" name="msg[original_name]" maxlength="90" size="80" /> <span class="med nowrap">{L_ORIGINAL_TITLE_DESC}</span></td>
</tr>
<tr>
	<td><b>Другие варианты названия</b>:</td>
	<td><input type="text" name="msg[additional_name]" maxlength="90" size="80" /> <span class="med nowrap">разделяются символом /</span></td>
</tr>
<tr>
	<td><b><!-- IF PICTURE_HREF --><a href="{PICTURE_HREF}" target="_blank">{L_PICTURE}</a><!-- ELSE -->{L_PICTURE}<!-- ENDIF --></b>:</td>
	<td><input type="text" name="msg[picture]" size="80" /> <span class="med">URL</span> <a class="med" href="http://up.local.ivacuum.ru/" target="_blank"><b>Загрузить картинку</b></a></td>
</tr>
<tr>
	<td><b><!-- IF COUNTRY_HREF --><a href="{COUNTRY_HREF}" target="_blank">{L_COUNTRY}</a><!-- ELSE -->{L_COUNTRY}<!-- ENDIF --></b>:</td>
	<td><input type="text" name="msg[country]" size="50" /></td>
</tr>
<tr>
	<td><b><!-- IF YEAR_HREF --><a href="{YEAR_HREF}" target="_blank">{L_YEAR}</a><!-- ELSE -->{L_YEAR}<!-- ENDIF --></b>:</td>
	<td><input type="text" name="msg[year]" maxlength="4" size="5" /></td>
</tr>
<tr>
	<td><b><!-- IF GENRE_HREF --><a href="{GENRE_HREF}" target="_blank">{L_GENRE}</a><!-- ELSE -->{L_GENRE}<!-- ENDIF --></b>:</td>
	<td><input type="text" name="msg[genre]" size="40" /></td>
</tr>
<tr>
	<td><b>Тип</b>:</td>
	<td><select name="msg[broadcast_type]"><option selected="selected" value="">Выбрать &raquo;</option><option value="movie">полнометражный фильм</option><option value="tv">ТВ сериал</option><option value="ova">Original Video Animation</option><option value="ona">Original Network Animation</option><option value="special">Доп серии к сериалу</option></select></td>
</tr>
<tr>
	<td><b>Эпизоды</b>:</td>
	<td><input name="msg[episodes]" maxlength="90" size="50" type="text" /> <span class="med nowrap">например, &laquo;11 из 51&raquo;, &laquo;2-6 из 13&raquo;</span></td>
</tr>
<tr>
	<td><b><!-- IF PLAYTIME_HREF --><a href="{PLAYTIME_HREF}" target="_blank">{L_PLAYTIME}</a><!-- ELSE -->{L_PLAYTIME}<!-- ENDIF --></b>:</td>
	<td><input type="text" name="msg[playtime]" size="50" /> <span class="med nowrap">например, &laquo;24 эп, ~25 мин. серия&raquo; или &laquo;104 мин.&raquo; (для фильмов)</span></td>
</tr>
<tr>
	<td><b><!-- IF DIRECTOR_HREF --><a href="{DIRECTOR_HREF}" target="_blank">{L_DIRECTOR}</a><!-- ELSE -->{L_DIRECTOR}<!-- ENDIF --></b>:</td>
	<td><input type="text" name="msg[director]" size="50" /></td>
</tr>
<tr>
	<td><b>Студия</b>:</td>
	<td><input type="text" name="msg[company]" size="50" /></td>
</tr>
<tr>
	<td><b><!-- IF DESCRIPTION_HREF --><a href="{DESCRIPTION_HREF}" target="_blank">{L_DESCRIPTION}</a><!-- ELSE -->{L_DESCRIPTION}<!-- ENDIF --></b>:</td>
	<td><textarea name="msg[description]" rows="10" cols="100" class="editor"></textarea></td>
</tr>
<tr>
	<td><b><!-- IF MOREINFO_HREF --><a href="{MOREINFO_HREF}" target="_blank">{L_MOREINFO}</a><!-- ELSE -->{L_MOREINFO}<!-- ENDIF --></b>:</td>
	<td><textarea name="msg[moreinfo]" rows="3" cols="100" class="editor"></textarea></td>
</tr>
<tr>
	<td><b>Тех. параметры</b>:</td>
	<td>
		<select name="msg[quality]"><option value="">&raquo; {L_QUALITY}</option><script type="text/javascript">document.writeln(make_format_list(quality));</script></select>&nbsp;
		<select name="msg[hardsub]"><option value="">&raquo; Тип релиза</option><option value="Хардсаб">Хардсаб</option><option value="Без хардсаба">Без хардсаба</option><option value="Полухардсаб">Полухардсаб</option></select>&nbsp;
		<select name="msg[format]"><option value="">&raquo; {L_FORMAT}</option><script type="text/javascript">document.writeln(make_format_list(video_formats));</script></select>&nbsp;
	</td>
</tr>
<tr>
	<td><b><!-- IF VIDEO_HREF --><a href="{VIDEO_HREF}" target="_blank">{L_VIDEO}</a><!-- ELSE -->{L_VIDEO}<!-- ENDIF --></b>:</td>
	<td><input type="text" name="msg[video]" size="80" /></td>
</tr>
<tr>
	<td><b><!-- IF AUDIO_HREF --><a href="{AUDIO_HREF}" target="_blank">{L_AUDIO}</a><!-- ELSE -->{L_AUDIO}<!-- ENDIF --></b>:</td>
	<td><input type="text" name="msg[audio]" size="80" /></td>
</tr>
<tr>
	<td><b>Список эпизодов</b>:</td>
	<td><textarea name="msg[episode_list]" rows="4" cols="100" class="editor"></textarea></td>
</tr>
<tr>
	<td><b>{L_SCREEN_SHOTS}</b>:</td>
	<td><textarea name="msg[screen_shots]" rows="3" cols="100" class="editor"></textarea> <span class="med">URLs</span></td>
</tr>
<tr>
	<td><b><!-- IF TORRENT_HREF --><a href="{TORRENT_HREF}" target="_blank">{L_TORRENT}</a><!-- ELSE -->{L_TORRENT}<!-- ENDIF --></b>:</td>
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