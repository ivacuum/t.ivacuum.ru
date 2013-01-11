<?php

//
// Translation performed by Alexey V. Borzov (borz_off)
// borz_off@cs.msu.su
//


$lang['General'] = 'Общие настройки';
$lang['Users'] = 'Пользователи';
$lang['Groups'] = 'Группы';
$lang['Forums'] = 'Форумы';

$lang['Configuration'] = 'Конфигурация';
$lang['Permissions'] = 'Права доступа';
$lang['Manage'] = 'Управление';
$lang['Disallow'] = 'Запрещённые имена';
$lang['Prune'] = 'Чистка';
$lang['Mass_Email'] = 'Массовая рассылка почты';
$lang['Ranks'] = 'Звания';
$lang['Smilies'] = 'Смайлики';
$lang['Ban_Management'] = 'Чёрные списки (Ban)';
$lang['Word_Censor'] = 'Автоцензор';
$lang['Export'] = 'Экспорт';
$lang['Create_new'] = 'Создать';
$lang['Add_new'] = 'Добавить';
$lang['Flags'] = 'Флаги';
$lang['Forum_Config'] = 'Настройки форумов';
$lang['Tracker_Config'] = 'Настройки трекера';
$lang['Release_Templates'] = 'Шаблоны для релизов';

//
// Index
//
$lang['ADMIN'] = 'Администрирование';
$lang['Welcome_phpBB'] = 'Добро пожаловать на phpBB';
$lang['Admin_intro'] = 'Спасибо за выбор phpBB в качестве решения для ваших форумов. На этой странице дан краткий обзор различных возможностей этой доски объявлений. Вернуться на эту страницу вы можете, щёлкнув на ссылку <u>Главная страница</u> в левой панели. Для перехода на список форумов щёлкните по логотипу phpBB также в левой панели. Остальные ссылки в левой части этого экрана позволят вам управлять всеми аспектами ваших форумов, на каждом экране будут даны инструкции по использованию.';
$lang['Main_index'] = 'Список форумов';
$lang['Forum_stats'] = 'Статистика Форумов';
$lang['Admin_Index'] = 'Главная страница';
$lang['Preview_forum'] = 'Предварительный просмотр форума';

$lang['Click_return_admin_index'] = '%sВернуться на главную страницу администраторского раздела%s';

$lang['Statistic'] = 'Статистика';
$lang['Value'] = 'Значение';
$lang['Number_posts'] = 'Кол-во сообщений';
$lang['Posts_per_day'] = 'Сообщений в день';
$lang['Number_topics'] = 'Кол-во тем';
$lang['Topics_per_day'] = 'Тем в день';
$lang['Number_users'] = 'Кол-во пользователей';
$lang['Users_per_day'] = 'Пользователей в день';
$lang['Board_started'] = 'Дата запуска';
$lang['Avatar_dir_size'] = 'Размер директории с аватарами';
$lang['Database_size'] = 'Объём БД';
$lang['Gzip_compression'] ='сжатие Gzip';
$lang['Not_available'] = 'Недоступно';

$lang['ON'] = 'ВКЛ'; // This is for GZip compression
$lang['OFF'] = 'ВЫКЛ';

//
// Auth pages
//
$lang['USER_SELECT'] = 'Выберите пользователя';
$lang['GROUP_SELECT'] = 'Выберите группу';
$lang['Select_a_Forum'] = 'Выберите форум';
$lang['AUTH_CONTROL_USER'] = 'Права пользователей';
$lang['AUTH_CONTROL_GROUP'] = 'Права групп';
$lang['Auth_Control_Forum'] = 'Доступ к форумам';
$lang['LOOK_UP_FORUM'] = 'Выбрать форум';

$lang['GROUP_AUTH_EXPLAIN'] = 'Здесь вы можете изменить права доступа и статус модератора для каждой группы пользователей. Не забывайте при изменении прав доступа для групп, что права доступа для отдельных пользователей могут давать пользователю возможность входа в форумы и т.п. Вы будете предупреждены в этом случае.';
$lang['USER_AUTH_EXPLAIN'] = 'Здесь вы можете изменить права доступа и статус модератора для отдельных пользователей. Не забывайте при изменении прав пользователя, что права доступа для группы могут давать пользователю возможность входа в форумы и т.п. Вы будете предупреждены в этом случае.';
$lang['Forum_auth_explain'] = 'Здесь вы можете регулировать доступ к каждому форуму. У вас будет обычный и продвинутый режим для этого, продвинутый даёт больше возможностей для контроля. Помните, что изменение прав доступа к форуму повлияет на то, какие пользователи смогут совершать в нём различные действия';

$lang['Simple_mode'] = 'Простой режим';
$lang['Advanced_mode'] = 'Продвинутый режим';
$lang['MODERATOR_STATUS'] = 'Статус модератора';

$lang['Allowed_Access'] = 'Доступ открыт';
$lang['Disallowed_Access'] = 'Доступ закрыт';
$lang['Is_Moderator'] = 'Модератор';
$lang['Not_Moderator'] = 'Не модератор';

$lang['Conflict_warning'] = 'Предупреждение о конфликте прав';
$lang['Conflict_access_userauth'] = 'У пользователя (пользователей) всё ещё есть права доступа к этому форуму, связанные с членством в группе. Вам, возможно, надо изменить права доступа для групп или исключить пользователя из группы для того, чтобы полностью закрыть ему права доступа. Группы, дающие такие права, перечислены ниже.';
$lang['Conflict_mod_userauth'] = 'У данного пользователя всё ещё есть право модерирования этого форума, связанное с его членством в группе. Вам, возможно, надо изменить права доступа для групп или исключить пользователя из группы для того, чтобы полностью закрыть ему право модерации. Группы, дающие это право, перечислены ниже.';

$lang['Conflict_access_groupauth'] = 'У пользователя (пользователей) всё ещё есть права доступа к этому форуму из-за установок их личных прав. Вам, возможно, надо изменить их права для того, чтобы полностью закрыть им доступ. Пользователи, имеющие такие права, перечислены ниже.';
$lang['Conflict_mod_groupauth'] = 'У пользователя (пользователей) всё ещё есть право модерирования этого форума из-за установок их личных прав. Вам, возможно, надо изменить их права для того, чтобы полностью закрыть им возможность модерирования. Пользователи, имеющие такие права, перечислены ниже.';

$lang['Public'] = 'Публичный';
$lang['Private'] = 'Приватный';
$lang['Registered'] = 'Зарегистрированный';
$lang['Administrators'] = 'Администраторы';
$lang['Hidden'] = 'Спрятанный';

// These are displayed in the drop down boxes for advanced
// mode forum auth, try and keep them short!
$lang['Forum_ALL'] = 'Все';
$lang['Forum_REG'] = 'Регистр.';
$lang['Forum_PRIVATE'] = 'Приватный';
$lang['Forum_MOD'] = 'Модератор';
$lang['Forum_ADMIN'] = 'Админ';

$lang['auth_view'] = $lang['View'] = 'Видеть';
$lang['auth_read'] = $lang['Read'] = 'Читать';
$lang['auth_post'] = $lang['Post'] = 'Создавать темы';
$lang['auth_reply'] = $lang['Reply'] = 'Отвечать';
$lang['auth_edit'] = $lang['Edit'] = 'Редактировать';
$lang['auth_delete'] = 'Удалять';
$lang['auth_sticky'] = $lang['Sticky'] = 'Прилеплять темы';
$lang['auth_announce'] = $lang['Announce'] = 'Создавать объявления';
$lang['auth_vote'] = $lang['Vote'] = 'Голосовать';
$lang['auth_pollcreate'] = $lang['Pollcreate'] = 'Создавать опросы';
$lang['auth_attachments'] = $lang['Auth_attach'] = 'Прикреплять файлы';
$lang['auth_download'] = $lang['Auth_download'] = 'Скачивать файлы';

$lang['Simple_Permission'] = 'Простое право доступа';

$lang['User_Level'] = 'Статус пользователя';
$lang['Auth_User'] = 'Пользователь';
$lang['Auth_Admin'] = 'Администратор';
$lang['Group_memberships'] = 'Членство в группах';
$lang['Usergroup_members'] = 'В этой группе состоят';

$lang['Forum_auth_updated'] = 'Права доступа к форуму изменены';
$lang['User_auth_updated'] = 'Права пользователя изменены';
$lang['Group_auth_updated'] = 'Права группы изменены';

$lang['Auth_updated'] = 'Права доступа изменены';
$lang['Click_return_userauth'] = '%sВернуться к управлению правами пользователей%s';
$lang['Click_return_groupauth'] = '%sВернуться к управлению правами групп%s';
$lang['Click_return_forumauth'] = '%sВернуться к управлению доступом к форумам%s';


//
// Banning
//
$lang['Ban_control'] = 'Чёрные списки';
$lang['Ban_explain'] = 'Здесь вы можете закрывать пользователям любой доступ к форумам. Вы можете внести в чёрный список конкретного пользователя, а также один ил несколько IP адресов или имён серверов. Этот метод не даст пользователю увидеть даже список форумов. Чтобы запретить регистрацию под другим именем, вы можете также внести в чёрный список адрес e-mail. Учтите, запрещение только e-mail адреса не закроет пользователю возможность заходить на форум и писать сообщения. Для этого вам придётся воспользоваться одним из первых двух методов.';
$lang['Ban_explain_warn'] = 'Учтите, что ввод диапазона IP адресов приведёт к добавлению всех адресов между первым и последним в &laquo;чёрный список&raquo;. Будут проделаны попытки уменьшить это количество вводом шаблонов, где это возможно. Если вам действительно надо ввести диапазон адресов, постарайтесь сделать его поменьше или, что ещё лучше, вводите отдельные адреса.';

$lang['Select_ip'] = 'Выберите IP адрес';
$lang['Select_email'] = 'Выберите адрес e-mail';

$lang['Ban_username'] = 'Закрытие доступа отдельным пользователям';
$lang['Ban_username_explain'] = 'Вы можете закрыть доступ нескольким пользователям за один раз, используя подходящую для вашего компьютера и браузера комбинацию клавиатуры и мыши.';

$lang['Ban_IP'] = 'Закрыть доступ с одного или нескольких адресов IP или хостов';
$lang['IP_hostname'] = 'Адреса IP или хосты';
$lang['Ban_IP_explain'] = 'Чтобы указать несколько разных адресов или хостов, разделите их запятыми. Чтобы указать последовательность адресов IP разделите начало и конец дефисом (-), чтобы указать шаблон используйте *';

$lang['Ban_email'] = 'Запретить e-mail адреса';
$lang['Ban_email_explain'] = 'Чтобы запретить несколько e-mail адресов, разделите их запятыми. Чтобы указать шаблон, используйте *, например *@mail.ru';

$lang['Unban_username'] = 'Вновь открыть доступ пользователям';
$lang['Unban_username_explain'] = 'Вы можете вновь открыть доступ нескольким пользователям за один раз, используя подходящую для вашего компьютера и браузера комбинацию клавиатуры и мыши.';

$lang['Unban_IP'] = 'Вновь открыть доступ с адресов IP';
$lang['Unban_IP_explain'] = 'Вы можете вновь разрешить доступ с нескольких адресов IP за один раз, используя подходящую для вашего компьютера и браузера комбинацию клавиатуры и мыши.';

$lang['Unban_email'] = 'Вновь разрешить адреса e-mail';
$lang['Unban_email_explain'] = 'Вы можете вновь разрешить несколько адресов e-mail за один раз, используя подходящую для вашего компьютера и браузера комбинацию клавиатуры и мыши.';

$lang['No_banned_users'] = 'Чёрный список пользователей пуст';
$lang['No_banned_ip'] = 'Чёрный список адресов IP пуст';
$lang['No_banned_email'] = 'Чёрный список адресов e-mail пуст';

$lang['Ban_update_sucessful'] = 'Чёрный список был успешно обновлён';
$lang['Click_return_banadmin'] = '%sВернуться к чёрным спискам%s';


//
// Configuration
//
$lang['General_Config'] = 'Общие настройки';
$lang['Config_explain'] = 'Эта форма позволит вам изменить общие настройки форумов. Для управления пользователями и отдельными форумами используйте соответствующие ссылки слева.';

$lang['Click_return_config'] = '%sВернуться к общим настройкам%s';

$lang['General_settings'] = 'Общие настройки форумов';
$lang['Server_name'] = 'Имя сервера';
$lang['Server_name_explain'] = 'Имя сервера, на котором запущены эти форумы';
$lang['Script_path'] = 'Путь к форумам';
$lang['Script_path_explain'] = 'Путь к каталогу, содержащему phpBB, относительно корня сайта';
$lang['Server_port'] = 'Порт веб-сервера';
$lang['Server_port_explain'] = 'Порт, на котором запущен ваш веб-сервер (обычно 80, изменяйте <b>только</b> если сервер работает на другом порту)';
$lang['Site_name'] = 'Название сайта';
$lang['Site_desc'] = 'Описание сайта';
$lang['Board_disable'] = 'Отключить форумы';
$lang['Board_disable_explain'] = 'Форумы станут недоступными пользователям. У Администраторов останется доступ через Панель Администрирования пока форум выключен.';
$lang['Acct_activation'] = 'Включить активизацию учётных записей';
$lang['Acc_None'] = 'Нет'; // These three entries are the type of activation
$lang['Acc_User'] = 'Пользователем';
$lang['ACC_ADMIN'] = 'Администратором';

$lang['Abilities_settings'] = 'Общие настройки форумов и пользователей';
$lang['Max_poll_options'] = 'Макс. кол-во вариантов ответа в опросе';
$lang['Flood_Interval'] = 'Задержка &laquo;флуда&raquo;';
$lang['Flood_Interval_explain'] = 'Время (в секундах), которое должно пройти между двумя сообщениями пользователя.';
$lang['Board_email_form'] = 'Рассылка e-mail сообщений через форумы';
$lang['Board_email_form_explain'] = 'Пользователи смогут посылать друг другу e-mail через форумы';
$lang['TOPICS_PER_PAGE'] = 'Тем на страницу';
$lang['Posts_per_page'] = 'Сообщений на страницу';
$lang['Hot_threshold'] = 'Сообщений в &laquo;популярной&raquo; теме';
$lang['Default_style'] = 'Стиль';
$lang['Default_language'] = 'Язык по умолчанию';
$lang['Date_format'] = 'Формат даты';
$lang['System_timezone'] = 'Часовой пояс';
$lang['Enable_gzip'] = 'Включить сжатие GZip';
$lang['Enable_prune'] = 'Включить чистку форумов';
$lang['Allow_BBCode'] = 'Разрешить BBCode';
$lang['Allow_smilies'] = 'Разрешить смайлики';
$lang['Smilies_path'] = 'Путь к смайликам';
$lang['Smilies_path_explain'] = 'Каталог ниже корня phpBB, например images/smilies';
$lang['Allow_sig'] = 'Разрешить подписи';
$lang['Max_sig_length'] = 'Макс. длина подписи';
$lang['Max_sig_length_explain'] = 'Максимальное кол-во символов в подписи пользователя';
$lang['Allow_name_change'] = 'Разрешить смену имени пользователя';

$lang['Avatar_settings'] = 'Настройки аватар';
$lang['Allow_local'] = 'Разрешить аватар из галереи';
$lang['Allow_remote'] = 'Разрешить удалённых аватар';
$lang['Allow_remote_explain'] = 'Ссылка на аватару, находящуюся на другом сайте';
$lang['Allow_upload'] = 'Разрешить закачку аватар';
$lang['Max_filesize'] = 'Макс. размер файла аватары';
$lang['Max_filesize_explain'] = 'Для закачанных файлов';
$lang['Max_avatar_size'] = 'Макс. размер изображения';
$lang['Max_avatar_size_explain'] = '(высота x ширина в пикселях)';
$lang['Avatar_storage_path'] = 'Путь к аватарам';
$lang['Avatar_storage_path_explain'] = 'Каталог ниже корня phpBB, например images/avatars';
$lang['Avatar_gallery_path'] = 'Путь к галерее аватар';
$lang['Avatar_gallery_path_explain'] = 'Каталог ниже корня phpBB для готовых картинок, например images/avatars/gallery';

$lang['COPPA_settings'] = 'COPPA Settings';
$lang['COPPA_fax'] = 'COPPA Fax Number';
$lang['COPPA_mail'] = 'COPPA Mailing Address';
$lang['COPPA_mail_explain'] = 'This is the mailing address where parents will send COPPA registration forms';

$lang['Email_settings'] = 'Настройки e-mail';
$lang['Admin_email'] = 'Адрес e-mail администратора';
$lang['Email_sig'] = 'Подпись в сообщениях e-mail';
$lang['Email_sig_explain'] = 'Этот текст будет подставляться во все письма, рассылаемые из форумов';
$lang['Use_SMTP'] = 'Использовать сервер SMTP для отправки почты';
$lang['Use_SMTP_explain'] = 'Отметьте, если вы хотите/вынуждены отсылать почту через сервер SMTP, а не локальную почтовую службу';
$lang['SMTP_server'] = 'Адрес сервера SMTP';
$lang['SMTP_username'] = 'Имя пользователя для SMTP';
$lang['SMTP_username_explain'] = 'Не указывайте имя пользователя если оно не требуется для работы с вашим сервером SMTP';
$lang['SMTP_password'] = 'Пароль для SMTP';
$lang['SMTP_password_explain'] = 'Не указывайте пароль если он не требуется для работы с вашим сервером SMTP';

$lang['Disable_privmsg'] = 'Личные сообщения';
$lang['Inbox_limits'] = 'Макс. число сообщений в папке &laquo;Входящие&raquo;';
$lang['Sentbox_limits'] = 'Макс. число сообщений в папке &laquo;Отправленные&raquo;';
$lang['Savebox_limits'] = 'Макс. число сообщений в папке &laquo;Сохранённые&raquo;';

$lang['Cookie_settings'] = 'Настройки куков';
$lang['Cookie_settings_explain'] = 'Вы можете изменить параметры куков (cookies), отправляемых пользователям. В большинстве случаев подходят значения по умолчанию. Если вам требуется их изменить, соблюдайте осторожность: неверные значения могут помешать пользователям входить в систему.';
$lang['Cookie_domain'] = 'Домен куки';
$lang['Cookie_name'] = 'Имя куки';
$lang['Cookie_path'] = 'Путь куки';
$lang['Cookie_secure'] = 'Безопасные куки [ https ]';
$lang['Cookie_secure_explain'] = 'Если ваш сервер работает через SSL, то включите эту установку, в противном случае оставьте выключенной.';
$lang['Session_length'] = 'Длина сессии [ в секундах ]';

// Visual Confirmation
$lang['Visual_confirm'] = 'Включить визуальное подтверждение';
$lang['Visual_confirm_explain'] = 'Потребовать от пользователей ввести при регистрации изображённый на картинке код.';

// Autologin Keys - added 2.0.18
$lang['Allow_autologin'] = 'Разрешить автоматический вход на форум';
$lang['Allow_autologin_explain'] = 'Разрешен ли пользователям автоматический вход на форум';
$lang['Autologin_time'] = 'Автоматический вход на форум действителен';
$lang['Autologin_time_explain'] = 'Срок в днях с последнего посещения, в течение которого пользователь может автоматически войти на форум. Установите равным нулю, если хотите отключить данную возможность.';
//
// Forum Management
//
$lang['Forum_admin'] = 'Управление форумами';
$lang['Forum_admin_explain'] = 'Здесь вы можете создавать, удалять и изменять порядок вывода категорий и форумов';
$lang['Edit_forum'] = 'Изменить форум';
$lang['Create_forum'] = 'Создать новый форум';
$lang['Create_category'] = 'Создать новую категорию';
$lang['Remove'] = 'Удалить';
$lang['Action'] = 'Действие';
$lang['Update_order'] = 'Изменить порядок';
$lang['Config_updated'] = 'Конфигурация форумов успешно изменена';
$lang['Edit'] = 'Изменить';
$lang['Move_up'] = 'вверх'; // 'Сдвинуть вверх';
$lang['Move_down'] = 'вниз'; // 'Сдвинуть вниз';
$lang['Resync'] = 'Синхронизация';
$lang['No_mode'] = 'Не было задано действие';
$lang['Forum_edit_delete_explain'] = 'Здесь вы можете изменить название и описание форума, закрыть его (или вновь открыть) и настроить автоматическую чистку. Для управления правами доступа к форуму воспользуйтесь соответствующей ссылкой в левой части.';

$lang['Move_contents'] = 'Перенести всё содержимое';
$lang['Forum_delete'] = 'Удалить форум';
$lang['Forum_delete_explain'] = 'Здесь вы сможете удалить форум (или категорию) и решить, куда перенести все темы (или форумы), которые там содержались.';
$lang['Category_delete'] = 'Удалить Категорию';

$lang['Status_locked'] = 'Закрыт';
$lang['Status_unlocked'] = 'Открыт';
$lang['Forum_settings'] = 'Общие параметры форума';
$lang['FORUM_NAME'] = 'Название форума';
$lang['Forum_desc'] = 'Описание';
$lang['Forum_status'] = 'Статус форума';
$lang['Forum_pruning'] = 'Автоматическая чистка';

$lang['prune_days'] = 'Удалять темы, в которых не было сообщений последние';
$lang['Set_prune_data'] = 'Вы выбрали для этого форума автоматическую чистку, но не указали количество дней. Пожалуйста, вернитесь и укажите.';

$lang['Move_and_Delete'] = 'Перенести и удалить';

$lang['Delete_all_posts'] = 'Удалить все темы';
$lang['Nowhere_to_move'] = 'Некуда переносить';

$lang['Edit_Category'] = 'Изменить категорию';
$lang['Edit_Category_explain'] = 'Используйте эту форму, чтобы изменить название категории';

$lang['Forums_updated'] = 'Информация о форумах и категориях успешно изменена';

$lang['Must_delete_forums'] = 'Вы должны удалить все форумы, прежде чем сможете удалить эту категорию';

$lang['Click_return_forumadmin'] = '%sВернуться к управлению форумами%s';

$lang['SHOW_ALL_FORUMS_ON_ONE_PAGE'] = 'Открыть все форумы на одной странице';

//
// Smiley Management
//
$lang['smiley_title'] = 'Утилита редактирования смайликов';
$lang['smile_desc'] = 'Здесь вы можете редактировать список смайликов';

$lang['smiley_config'] = 'Управление смайликами';
$lang['smiley_code'] = 'Код смайлика';
$lang['smiley_url'] = 'Файл с изображением смайлика';
$lang['smiley_emot'] = 'Эмоция смайлика';
$lang['smile_add'] = 'Добавить новый смайлик';
$lang['Smile'] = 'Смайлик';
$lang['Emotion'] = 'Эмоция';

$lang['Select_pak'] = 'Выберите файл с набором (.pak)';
$lang['replace_existing'] = 'Заменить существующий смайлик';
$lang['keep_existing'] = 'Сохранить существующий смайлик';
$lang['smiley_import_inst'] = 'Вы должны распаковать набор смайликов и закачать все файлы в подходящую для вашей установки директорию. Потом выберите в этой форме нужную информацию для импорта набора смайликов.';
$lang['smiley_import'] = 'Импорт набора смайликов';
$lang['choose_smile_pak'] = 'Выберите файл .pak с набором';
$lang['import'] = 'Импортировать смайлики';
$lang['smile_conflicts'] = 'Что делать в случае конфликта';
$lang['del_existing_smileys'] = 'Удалить перед импортом существующие смайлики';
$lang['import_smile_pack'] = 'Импортировать набор смайликов';
$lang['export_smile_pack'] = 'Создать набор смайликов';
$lang['export_smiles'] = 'Для создания набора смайликов из смайликов, установленных в данный момент, %sскачайте файл smiles.pak%s. Переименуйте его как вам нужно, сохранив при этом расширение .pak, затем создайте файл zip, содержащий все изображения смайликов, а также этот файл.';

$lang['smiley_add_success'] = 'Смайлик был успешно добавлен';
$lang['smiley_edit_success'] = 'Смайлик был успешно изменён';
$lang['smiley_import_success'] = 'Набор смайликов был успешно импортирован';
$lang['smiley_del_success'] = 'Смайлик был успешно удалён';
$lang['Click_return_smileadmin'] = '%sВернуться к списку смайликов%s';


//
// User Management
//
$lang['USER_ADMIN'] = 'Управление пользователями';
$lang['User_admin_explain'] = 'Здесь вы можете изменить информацию о пользователе. Чтобы изменить права доступа используйте панель управления правами доступа';

$lang['LOOK_UP_USER'] = 'Выбрать пользователя';

$lang['Admin_user_fail'] = 'Не могу изменить профиль пользователя';
$lang['Admin_user_updated'] = 'Профиль пользователя был успешно изменён';
$lang['Click_return_useradmin'] = '%sВернуться к управлению пользователями%s';

$lang['User_delete'] = 'Удаление';
$lang['User_delete_explain'] = 'Удалить этого пользователя';
$lang['User_deleted'] = 'Пользователь был успешно удалён';
$lang['Delete_user_posts'] = 'Удалить все сообщения пользователя';

$lang['User_status'] = 'Пользователь активен';
$lang['User_allowpm'] = 'Может посылать личные сообщения';
$lang['User_allowavatar'] = 'Может показывать аватару';

$lang['Admin_avatar_explain'] = 'Здесь вы можете просмотреть и удалить текущую аватару пользователя';

$lang['User_special'] = 'Поля только для админа';
$lang['User_special_explain'] = 'Эти поля сами пользователи редактировать не могут. Здесь вы можете установить их статус и сделать прочие недоступные им настройки.';


//
// Group Management
//
$lang['GROUP_ADMINISTRATION'] = 'Управление группами';
$lang['GROUP_ADMIN_EXPLAIN'] = 'Здесь вы можете управлять всеми вашими группами: это включает удаление, добавление и изменение групп. Вы можете назначать модераторов, изменять открытый/закрытый статус группы и устанавливать её название и описание.';
$lang['Error_updating_groups'] = 'Ошибка при изменении группы.';
$lang['Updated_group'] = 'Группа была успешно изменена';
$lang['Added_new_group'] = 'Группа была успешно создана';
$lang['Deleted_group'] = 'Группа была успешно удалена';
$lang['CREATE_NEW_GROUP'] = 'Создать новую группу';
$lang['Edit_group'] = 'Изменить группу';
$lang['GROUP_STATUS'] = 'Статус группы';
$lang['GROUP_DELETE'] = 'Удалить группу.';
$lang['GROUP_DELETE_CHECK'] = 'Удалить эту группу';
$lang['submit_group_changes'] = 'Сохранить изменения';
$lang['reset_group_changes'] = 'Отменить изменения';
$lang['No_group_name'] = 'Вы должны указать название группы';
$lang['No_group_moderator'] = 'Вы должны выбрать модератора группы';
$lang['No_group_mode'] = 'Вы должны выбрать режим группы: открытый или закрытый';
$lang['No_group_action'] = 'Не было выбрано действие';
$lang['DELETE_OLD_GROUP_MOD'] = 'Удалить старого модератора?';
$lang['DELETE_OLD_GROUP_MOD_EXPL'] = 'Если вы меняете модератора группы и поставите здесь галочку, то предыдущий модератор будет исключён из группы. Если вы её не поставите, то он станет обычным членом группы.';
$lang['Click_return_groupsadmin'] = '%sВернуться к управлению группами%s';
$lang['SELECT_GROUP'] = 'Выберите группу';
$lang['LOOK_UP_GROUP'] = 'Выбрать группу';


//
// Prune Administration
//
$lang['Forum_Prune'] = 'Чистка форумов';
$lang['Forum_Prune_explain'] = 'Будут удалены темы, в которых не было новых сообщений за выбранное число дней. Если вы не введёте число, то будут удалены все темы. Не будут удалены <b>прилепленные</b> темы и <b>объявления</b>. Вам придётся удалять такие темы вручную.';
$lang['Do_Prune'] = 'Провести чистку';
$lang['All_Forums'] = 'Все форумы';
$lang['Prune_topics_not_posted'] = 'Удалить темы, в которых не было ответов за данное кол-во дней';
$lang['Topics_pruned'] = 'Тем вычищено';
$lang['Posts_pruned'] = 'Сообщений вычищено';
$lang['Prune_success'] = 'Форум успешно почищен';


//
// Word censor
//
$lang['Words_title'] = 'Автоцензор';
$lang['Words_explain'] = 'Здесь вы можете добавить, изменить или удалить слова, которые будут автоматически подвергаться цензуре на ваших форумах. Кроме того, пользователи не смогут зарегистрироваться под именами, содержащими эти слова. В списке слов могут использоваться шаблоны (*), т.е. к \'*тест*\' подойдёт \'протестировать\', к \'тест*\' &mdash; \'тестирование\', к \'*тест\' &mdash; \'протест\'.<br>(Примечание переводчика) Рекомендую пользоваться этой фичей <b>очень</b> аккуратно: например, некие очевидные замены буду неадекватно реагировать на слова \'потребитель\', \'употреблять\' и т.п.';
$lang['Word'] = 'Слово';
$lang['Edit_word_censor'] = 'Изменить автоцензор';
$lang['Replacement'] = 'Замена';
$lang['Add_new_word'] = 'Добавить новое слово';
$lang['Update_word'] = 'Обновить автоцензор';

$lang['Must_enter_word'] = 'Вы должны ввести слово и его замену';
$lang['No_word_selected'] = 'Не выбрано слово для редактирования';

$lang['Word_updated'] = 'Выбранный автоцензор был успешно изменён';
$lang['Word_added'] = 'Автоцензор был успешно добавлен';
$lang['Word_removed'] = 'Выбранный автоцензор был успешно удалён';

$lang['Click_return_wordadmin'] = '%sВернуться к управлению автоцензором%s';


//
// Mass Email
//
$lang['Mass_email_explain'] = 'Вы можете разослать e-mail сообщение либо всем вашим пользователям, либо пользователям, входящим в определённую группу. Сообщение будет отправлено на административный адрес, с BCC: всем получателям. Если вы отправляете письмо большой группе людей, то будьте терпеливы: не останавливайте загрузку страницы после нажатия кнопки. Массовая рассылка может занять много времени, вы увидите сообщение, когда выполнение завершится.';
$lang['Compose'] = 'Текст сообщения';

$lang['Recipients'] = 'Получатели';
$lang['All_users'] = 'Все пользователи';

$lang['Email_successfull'] = 'Ваше сообщение было отправлено';
$lang['Click_return_massemail'] = '%sВернуться к массовой рассылке%s';


//
// Ranks admin
//
$lang['Ranks_title'] = 'Управление званиями';
$lang['Ranks_explain'] = 'Здесь вы можете добавлять, редактировать, просматривать и удалять звания. Вы также можете создавать специальные звания, которые могут затем быть присвоены пользователям на странице управления пользователями.';

$lang['Add_new_rank'] = 'Новое звание';

$lang['Rank_title'] = 'Звание';
$lang['Rank_special'] = 'Специальное звание';
$lang['Rank_minimum'] = 'Минимум сообщений';
$lang['Rank_maximum'] = 'Максимум сообщений';
$lang['Rank_image'] = 'Картинка к званию (относительно корня phpBB2)';
$lang['Rank_image_explain'] = 'Здесь вы можете присвоить всем имеющим такое звание специальное изображение. Вы можете указать либо относительный, либо абсолютный путь к изображению';

$lang['Must_select_rank'] = 'Извините, вы не выбрали звание. Вернитесь и попробуйте ещё раз.';
$lang['No_assigned_rank'] = 'Специального звания не присвоено';

$lang['Rank_updated'] = 'Звание было успешно изменено';
$lang['Rank_added'] = 'Звание было успешно добавлено';
$lang['Rank_removed'] = 'Звание было успешно удалено';
$lang['No_update_ranks'] = 'Звание было успешно удалено. Тем не менее, информация о пользователях, у которых было это звание, не была изменена. Вам придётся изменить эту информацию вручную.';

$lang['Click_return_rankadmin'] = '%sВернуться к управлению званиями%s';

// FLAGHACK-start
//
// Flags admin
//
$lang['Flags_title'] = 'Flag Administration';
$lang['Flags_explain'] = 'Using this form you can add, edit, view and delete flags. You can also create custom flags which can be applied to a user via the user management facility';
$lang['Add_new_flag'] = 'Add new flag';
$lang['Flag_name'] = 'Flag Name';
$lang['Flag_pic'] = 'Image';
$lang['Flag_image'] = 'Flag Image (in the images/flags/ directory)';
$lang['Flag_image_explain'] = 'Use this to define a small image associated with the flag';
$lang['Must_select_flag'] = 'You must select a flag';
$lang['Flag_updated'] = 'The flag was successfully updated';
$lang['Flag_added'] = 'The flag was successfully added';
$lang['Flag_removed'] = 'The flag was successfully deleted';
$lang['No_update_flags'] = 'The flag was successfully deleted. However, user accounts using this flag were not updated.  You will need to manually reset the flag on these accounts';
$lang['Flag_confirm'] = 'Delete Flag' ;
$lang['Confirm_delete_flag'] = 'Are you sure you want to remove the selected flag?' ;
$lang['Click_return_flagadmin'] = 'Click %sHere%s to return to Flag Administration';
// FLAGHACK-end

//
// Disallow Username Admin
//
$lang['Disallow_control'] = 'Запрещённые имена пользователя';
$lang['Disallow_explain'] = "Здесь вы можете задать имена, которые будут запрещены к использованию. Запрещённые имена могут содержать шаблон '*'. Учтите: вы не сможете запретить имя, если уже существует пользователь с таким именем. Вам придётся сначала удалить пользователя, а уже потом запретить имя.";

$lang['Delete_disallow'] = 'Удалить';
$lang['Delete_disallow_title'] = 'Удалить запрещённое имя пользователя';
$lang['Delete_disallow_explain'] = 'Вы можете убрать запрещённое имя, выбрав его из списка и нажав кнопку &laquo;сохранить&raquo;';

$lang['Add_disallow'] = 'Добавить';
$lang['Add_disallow_title'] = 'Добавить запрещённое имя пользователя';
$lang['Add_disallow_explain'] = 'Вы можете запретить имя пользователя, используя шаблон \'*\', который подходит к любому символу';

$lang['No_disallowed'] = 'Нет запрещённых имён';

$lang['Disallowed_deleted'] = 'Запрещённое имя пользователя было успешно удалено';
$lang['Disallow_successful'] = 'Запрещённое имя пользователя было успешно добавлено';
$lang['Disallowed_already'] = 'Имя, которое вы пытаетесь запретить, либо уже запрещено, либо есть в списке нецензурных слов, либо существует пользователь с подходящим именем';

$lang['Click_return_disallowadmin'] = '%sВернуться к управлению запрещёнными именами%s';


//
// Install Process
//
$lang['Welcome_install'] = 'Добро пожаловать в установку phpBB 2!';
$lang['Initial_config'] = 'Основные настройки';
$lang['DB_config'] = 'Настройки базы данных';
$lang['Admin_config'] = 'Настройки админа';
$lang['continue_upgrade'] = 'Как только вы скачаете файл настроек на ваш компьютер, вы можете нажать кнопку &laquo;Продолжить обновление&raquo; для продолжения процесса. Пожалуйста, не закачивайте файл настроек на сервер до завершения процесса обновления.';
$lang['upgrade_submit'] = 'Продолжить обновление';

$lang['Installer_Error'] = 'В процессе установки возникла ошибка';
$lang['Previous_Install'] = 'Была обнаружена предыдущая установка';
$lang['Install_db_error'] = 'При попытке обновить базу данных возникла ошибка';

$lang['Re_install'] = 'Предыдущая установка всё ещё активна. <br /><br />Если вы хотите установить phpBB 2 заново, вы должны нажать кнопку &laquo;Да&raquo; внизу. Учтите, что при этом будут уничтожены все имеющиеся данные, никаких копий сделано не будет! Ранее использовавшиеся имя и пароль администратора будут вновь созданы после переустановки, остальные настройки будут потеряны. <br /><br />Как следует подумайте, прежде чем нажимать &laquo;Да&raquo;!';

$lang['Inst_Step_0'] = 'Спасибо вам за выбор phpBB 2. Для продолжения установки укажите, пожалуйста, требуемые сведения. Учтите, что база данных, в которую вы устанавливаете phpBB 2, уже должна существовать. Если вы устанавливаете в БД, использующую ODBC (например, MS Access), вам надо сначала создать для неё DSN.';

$lang['Start_Install'] = 'Начать установку';
$lang['Finish_Install'] = 'Закончить установку';

$lang['Default_lang'] = 'Язык по умолчанию';
$lang['DB_Host'] = 'Имя сервера БД / DSN';
$lang['DB_Name'] = 'Название базы данных';
$lang['DB_Username'] = 'Имя пользователя БД';
$lang['DB_Password'] = 'Пароль к БД';
$lang['Database'] = 'База данных';
$lang['Install_lang'] = 'Выберите язык для установки';
$lang['dbms'] = 'Тип базы данных';
$lang['Table_Prefix'] = 'Префикс для таблиц в базе данных';
$lang['Admin_Username'] = 'Имя администратора';
$lang['Admin_Password'] = 'Пароль администратора';
$lang['Admin_Password_confirm'] = 'Пароль администратора [ повторите ]';

$lang['Inst_Step_2'] = 'Была создана учётная запись администратора. Основная установка на этом закончена. Теперь вы будете переправлены на страницу, с которой вы сможете настроить новую установку. Обязательно проверьте раздел Основных настроек и внесите необходимые изменения. Спасибо вам за выбор phpBB 2.';

$lang['Unwriteable_config'] = 'Запись в файл настроек невозможна. Вы сможете скачать копию файла, если нажмёте соответствующую кнопку. Вам надо будет закачать этот файл в каталог, в который вы установили phpBB 2. Как только это будет сделано, вы сможете войти в систему, используя ранее введённые имя и пароль администратора, и перейти в администраторский раздел (ссылка будет внизу каждой страницы), чтобы проверить основные настройки. Спасибо вам за выбор phpBB 2.';
$lang['Download_config'] = 'Скачать файл настроек';

$lang['ftp_choose'] = 'Выберите метод скачивания';
$lang['ftp_option'] = '<br />В этой версии PHP включены возможности FTP, вы можете попробовать сначала автоматически закачать файл настроек по FTP в нужный каталог.';
$lang['ftp_instructs'] = 'Вы решили закачать файл настроек по FTP в каталог, содержащий phpBB 2. Пожалуйста, укажите информацию, требуемую для осуществления этого процесса. Учтите, что путь FTP должен быть полным путём к вашей установке phpBB 2, как если бы вы пользовались обычным клиентом FTP.';
$lang['ftp_info'] = 'Укажите настройки FTP';
$lang['Attempt_ftp'] = 'Попробовать закачать файл настроек по FTP';
$lang['Send_file'] = 'Просто прислать файл, я закачаю его вручную';
$lang['ftp_path'] = 'Путь FTP к каталогу phpBB 2';
$lang['ftp_username'] = 'Имя пользователя для FTP';
$lang['ftp_password'] = 'Пароль для FTP';
$lang['Transfer_config'] = 'Начать закачку';
$lang['NoFTP_config'] = 'Попытка закачать файл настроек по FTP завершилась неудачей. Пожалуйста, скачайте файл настроек и поместите его в нужный каталог вручную.';

$lang['Install'] = 'Установка';
$lang['Upgrade'] = 'Обновление';


$lang['Install_Method'] = 'Выберите метод установки';

$lang['Install_No_Ext'] = 'Конфигурация PHP на вашем сервере не поддерживает выбранную вами СУБД';

$lang['Install_No_PCRE'] = 'Для работы phpBB2 требуется модуль Перл-совместимых регулярных выражений, который, видимо, отключён в вашей конфигурации PHP!';

// Version Check
//
$lang['Version_up_to_date'] = 'Ваша версия форума phpBB на данный момент самая новая, на сайте разработчика для нее нет никаких обновлений.';
$lang['Version_not_up_to_date'] = 'Ваша версия форума phpBB <b>не самая последняя</b>. Пожалуйста, посетите сайт разработчика по следующей ссылке: <a href="http://www.phpbb.com/downloads.php" target="_new">http://www.phpbb.com/downloads.php</a> для загрузки последних обновлений.';
$lang['Latest_version_info'] = 'Последняя доступная версия на сайте производителя: <b>phpBB %s</b>. ';
$lang['Current_version_info'] = 'А у Вас сейчас установлена: <b>phpBB %s</b>.';
$lang['Connect_socket_error'] = 'Невозможно установить подключение с phpBB Сервер сообщил об ошибке:<br />%s';
$lang['Socket_functions_disabled'] = 'Невозможно использовать функции соединения.';
$lang['Mailing_list_subscribe_reminder'] = 'Для получения последней информации относительно обновлений программных продуктов phpBB, Вы можете подписаться на рассылку новостей по e-mail, перейдя по следующей ссылке <a href="http://www.phpbb.com/support/" target="_new">Подписка на рассылку новостей phpBB</a>';
$lang['Version_information'] = 'Информация о версии phpBB';

//
// Login attempts configuration
//
$lang['Max_login_attempts'] = 'Allowed login attempts';
$lang['Max_login_attempts_explain'] = 'The number of allowed board login attempts.';
$lang['Login_reset_time'] = 'Login lock time';
$lang['Login_reset_time_explain'] = 'Time in minutes the user have to wait until he is allowed to login again after exceeding the number of allowed login attempts.';

//
// Permissions List
//
$lang['Permissions_List'] = 'Permissions List';
$lang['Auth_Control_Category'] = 'Category Permissions Control';
$lang['Forum_auth_list_explain'] = 'This provides a summary of the authorisation levels of each forum. You can edit these permissions, using either a simple or advanced method by clicking on the forum name. Remember that changing the permission level of forums will affect which users can carry out the various operations within them.';
$lang['Cat_auth_list_explain'] = 'This provides a summary of the authorisation levels of each forum within this category. You can edit the permissions of individual forums, using either a simple or advanced method by clicking on the forum name. Alternatively, you can set the permissions for all the forums in this category by using the drop-down menus at the bottom of the page. Remember that changing the permission level of forums will affect which users can carry out the various operations within them.';
$lang['Forum_auth_list_explain_ALL'] = 'All users';
$lang['Forum_auth_list_explain_REG'] = 'All registered users';
$lang['Forum_auth_list_explain_PRIVATE'] = 'Only users granted special permission';
$lang['Forum_auth_list_explain_MOD'] = 'Only moderators of this forum';
$lang['Forum_auth_list_explain_ADMIN'] = 'Only administrators';
$lang['Forum_auth_list_explain_auth_view'] = '%s can view this forum';
$lang['Forum_auth_list_explain_auth_read'] = '%s can read posts in this forum';
$lang['Forum_auth_list_explain_auth_post'] = '%s can post in this forum';
$lang['Forum_auth_list_explain_auth_reply'] = '%s can reply to posts this forum';
$lang['Forum_auth_list_explain_auth_edit'] = '%s can edit posts in this forum';
$lang['Forum_auth_list_explain_auth_delete'] = '%s can delete posts in this forum';
$lang['Forum_auth_list_explain_auth_sticky'] = '%s can post sticky topics in this forum';
$lang['Forum_auth_list_explain_auth_announce'] = '%s can post announcements in this forum';
$lang['Forum_auth_list_explain_auth_vote'] = '%s can vote in polls in this forum';
$lang['Forum_auth_list_explain_auth_pollcreate'] = '%s can create polls in this forum';
$lang['Forum_auth_list_explain_auth_attachments'] = '%s can post attachments';
$lang['Forum_auth_list_explain_auth_download'] = '%s can download attachments';

//
// Misc
//
$lang['SF_Show_on_index'] = 'Показывать на главной';
$lang['SF_Parent_forum'] = 'Родительский форум';
$lang['SF_No_parent'] = 'Нет родительского форума';
$lang['TEMPLATE'] = 'Шаблон';

