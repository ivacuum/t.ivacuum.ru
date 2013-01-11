<?php

setlocale(LC_ALL, 'ru_RU.CP1251');
$lang['CONTENT_ENCODING'] = 'windows-1251';
$lang['CONTENT_DIRECTION'] = 'ltr';
$lang['DATE_FORMAT'] =  'Y-m-d'; // This should be changed to the default date format for your language, php date() format
$lang['TRANSLATION_INFO'] = '';

//
// Common, these terms are used
// extensively on several pages
//
$lang['FORUM'] = 'Forum';
$lang['CATEGORY'] = 'Category';
$lang['TOPIC'] = 'Topic';
$lang['TOPICS'] = 'Topics';
$lang['TOPICS_SHORT'] = 'Topics';
$lang['REPLIES'] = 'Replies';
$lang['REPLIES_SHORT'] = 'Replies';
$lang['VIEWS'] = 'Views';
$lang['Post'] = 'Post';
$lang['POSTS'] = 'Posts';
$lang['POSTS_SHORT'] = 'Posts';
$lang['POSTED'] = 'Posted';
$lang['USERNAME'] = 'Username';
$lang['PASSWORD'] = 'Password';
$lang['EMAIL'] = 'Email';
$lang['Poster'] = 'Poster';
$lang['AUTHOR'] = 'Author';
$lang['Time'] = 'Time';
$lang['Hours'] = 'Hours';
$lang['MESSAGE'] = 'Message';
$lang['TORRENT'] = 'Torrent';
$lang['PERMISSIONS'] = 'Permissions';

$lang['1_Day'] = '1 Day';
$lang['7_Days'] = '7 Days';
$lang['2_Weeks'] = '2 Weeks';
$lang['1_Month'] = '1 Month';
$lang['3_Months'] = '3 Months';
$lang['6_Months'] = '6 Months';
$lang['1_Year'] = '1 Year';

$lang['GO'] = 'Go';
$lang['Jump_to'] = 'Jump to';
$lang['SUBMIT'] = 'Submit';
$lang['DO_SUBMIT'] = 'Submit';
$lang['RESET'] = 'Reset';
$lang['CANCEL'] = 'Cancel';
$lang['PREVIEW'] = 'Preview';
$lang['CONFIRM'] = 'Confirm';
$lang['Spellcheck'] = 'Spellcheck';
$lang['YES'] = 'Yes';
$lang['NO'] = 'No';
$lang['Enabled'] = 'Enabled';
$lang['Disabled'] = 'Disabled';
$lang['Error'] = 'Error';
$lang['SELECT_ACTION'] = 'Select action';

$lang['Next'] = 'Next';
$lang['Previous'] = 'Previous';
$lang['GOTO_PAGE'] = 'Goto page';
$lang['GOTO_SHORT'] = 'Page';
$lang['JOINED'] = 'Joined';
$lang['LONGEVITY'] = 'Longevity';
$lang['IP_Address'] = 'IP Address';
$lang['POSTED_AFTER'] = 'after';

$lang['Select_forum'] = 'Select a forum';
$lang['View_latest_post'] = 'View latest post';
$lang['View_newest_post'] = 'View newest post';
$lang['Page_of'] = 'Page <b>%d</b> of <b>%s</b>';

$lang['ICQ'] = 'ICQ Number';
$lang['AIM'] = 'AIM Address';
$lang['MSNM'] = 'MSN Messenger';
$lang['YIM'] = 'Yahoo Messenger';

$lang['Forum_Index'] = '%s Forum Index';  // eg. sitename Forum Index, %s can be removed if you prefer

$lang['Post_new_topic'] = 'Post new topic';
$lang['Post_regular_topic'] = 'Post regular topic';
$lang['Reply_to_topic'] = 'Reply to topic';
$lang['Reply_with_quote'] = 'Reply with quote';

$lang['Click_return_topic'] = 'Click %sHere%s to return to the topic'; // %s's here are for uris, do not remove!
$lang['Click_return_login'] = 'Click %sHere%s to try again';
$lang['Click_return_forum'] = 'Click %sHere%s to return to the forum';
$lang['Click_view_message'] = 'Click %sHere%s to view your message';
$lang['Click_return_modcp'] = 'Click %sHere%s to return to the Moderator Control Panel';
$lang['Click_return_group'] = 'Click %sHere%s to return to group information';

$lang['Admin_panel'] = 'Go to Administration Panel';

$lang['Board_disable'] = 'Sorry, but this board is currently unavailable.  Please try again later.';

$lang['LOADING'] = 'Loading...';
$lang['JUMPBOX_TITLE'] = 'Select forum';
$lang['DISPLAYING_OPTIONS'] = 'Displaying options';

//
// Global Header strings
//
$lang['Registered_users'] = 'Registered Users:';
$lang['Browsing_forum'] = 'Users browsing this forum:';
$lang['Online_users'] = 'In total there are <b>%1$d</b> users online: %2$d Registered and %3$d Guests';
$lang['Record_online_users'] = 'Most users ever online was <b>%s</b> on %s'; // first %s = number of users, second %s is the date.
$lang['Users'] = 'users';

$lang['ONLINE_ADMIN'] = 'Administrator';
$lang['ONLINE_MOD'] = 'Moderator';
$lang['ONLINE_GROUP_MEMBER'] = 'Group member';

$lang['You_last_visit'] = 'You last visited on: <span class="tz_time">%s</span>';
$lang['Current_time'] = 'The time now is: <span class="tz_time">%s</span>';

$lang['SEARCH_NEW'] = 'View newest posts';
$lang['SEARCH_SELF'] = 'my posts';
$lang['SEARCH_SELF_BY_LAST'] = 'last post time';
$lang['SEARCH_SELF_BY_MY'] = 'my post time';
$lang['SEARCH_UNANSWERED'] = 'View unanswered posts';
$lang['SEARCH_UNANSWERED_SHORT'] = 'unanswered';
$lang['SEARCH_LATEST'] = 'latest';

$lang['REGISTER'] = 'Register';
$lang['PROFILE'] = 'Profile';
$lang['Edit_profile'] = 'Edit your profile';
$lang['SEARCH'] = 'Search';
$lang['MEMBERLIST'] = 'Memberlist';
$lang['FAQ'] = 'FAQ';
$lang['BBCode_guide'] = 'BBCode Guide';
$lang['USERGROUPS'] = 'Usergroups';
$lang['LASTPOST'] = 'Last Post';
$lang['Moderator'] = 'Moderator';
$lang['MODERATORS'] = 'Moderators';
$lang['TERMS'] = 'Terms';

//
// Stats block text
//
$lang['Posted_articles_zero_total'] = 'Our users have posted a total of <b>0</b> articles'; // Number of posts
$lang['Posted_articles_total'] = 'Our users have posted a total of <b>%d</b> articles'; // Number of posts
$lang['Posted_article_total'] = 'Our users have posted a total of <b>%d</b> article'; // Number of posts
$lang['Registered_users_zero_total'] = 'We have <b>0</b> registered users'; // # registered users
$lang['Registered_users_total'] = 'We have <b>%d</b> registered users'; // # registered users
$lang['Registered_user_total'] = 'We have <b>%d</b> registered user'; // # registered users
$lang['Newest_user'] = 'The newest registered user is <b>%s%s%s</b>'; // a href, username, /a

$lang['No_new_posts_last_visit'] = 'No new posts since your last visit';
$lang['NO_NEW_POSTS'] = 'No new posts';
$lang['NEW_POSTS'] = 'New posts';
$lang['New_post'] = 'New post';
$lang['No_new_posts_hot'] = 'No new posts [ Popular ]';
$lang['New_posts_hot'] = 'New posts [ Popular ]';
$lang['NO_NEW_POSTS_LOCKED'] = 'Locked';
$lang['NEW_POSTS_LOCKED'] = 'New posts [ Locked ]';
$lang['FORUM_LOCKED'] = 'Forum is locked';


//
// Login
//
$lang['Enter_password'] = 'Please enter your username and password to log in.';
$lang['LOGIN'] = 'Log in';
$lang['LOGOUT'] = 'Log out';
$lang['CONFIRM_LOGOUT'] = 'Are you sure you want to log out?';

$lang['FORGOTTEN_PASSWORD'] = 'Forgot password?';
$lang['AUTO_LOGIN'] = 'Log me on automatically each visit';
$lang['Error_login'] = 'You have specified an incorrect or inactive username, or an invalid password.';
$lang['REMEMBER'] = 'Remember';
$lang['USER_WELCOME'] = 'Welcome,';

//
// Index page
//
$lang['Index'] = 'Index';
$lang['HOME'] = 'Home';
$lang['NO_POSTS'] = 'No Posts';
$lang['NO_FORUMS'] = 'This board has no forums';

$lang['PRIVATE_MESSAGE'] = 'Private Message';
$lang['PRIVATE_MESSAGES'] = 'Private Messages';
$lang['WHOSONLINE'] = 'Who is Online';

$lang['MARK_ALL_FORUMS_READ'] = 'Mark all forums read';
$lang['Forums_marked_read'] = 'All forums have been marked read';

$lang['LATEST_NEWS'] = 'Latest news';
$lang['SUBFORUMS'] = 'Subforums';

//
// Viewforum
//
$lang['View_forum'] = 'View Forum';

$lang['Forum_not_exist'] = 'The forum you selected does not exist.';
$lang['Reached_on_error'] = 'You have reached this page in error.';

$lang['DISPLAY_TOPICS'] = 'Display topics from previous';
$lang['All_Topics'] = 'All Topics';
$lang['TOPICS_PER_PAGE'] = 'topics per page';
$lang['MODERATE_FORUM'] = 'Moderate this forum';
$lang['TITLE_SEARCH_HINT'] = 'search title...';

$lang['Topic_Announcement'] = 'Announcement:';
$lang['Topic_Sticky'] = 'Sticky:';
$lang['Topic_Moved'] = 'Moved:';
$lang['Topic_Poll'] = '[ Poll ]';

$lang['MARK_TOPICS_READ'] = 'Mark all topics read';
$lang['Topics_marked_read'] = 'The topics for this forum have now been marked read';

$lang['Rules_post_can'] = 'You <b>can</b> post new topics in this forum';
$lang['Rules_post_cannot'] = 'You <b>cannot</b> post new topics in this forum';
$lang['Rules_reply_can'] = 'You <b>can</b> reply to topics in this forum';
$lang['Rules_reply_cannot'] = 'You <b>cannot</b> reply to topics in this forum';
$lang['Rules_edit_can'] = 'You <b>can</b> edit your posts in this forum';
$lang['Rules_edit_cannot'] = 'You <b>cannot</b> edit your posts in this forum';
$lang['Rules_delete_can'] = 'You <b>can</b> delete your posts in this forum';
$lang['Rules_delete_cannot'] = 'You <b>cannot</b> delete your posts in this forum';
$lang['Rules_vote_can'] = 'You <b>can</b> vote in polls in this forum';
$lang['Rules_vote_cannot'] = 'You <b>cannot</b> vote in polls in this forum';
$lang['Rules_moderate'] = 'You <b>can</b> moderate this forum';

$lang['No_topics_post_one'] = 'There are no posts in this forum.<br />Click on the <b>Post New Topic</b> link on this page to post one.';


//
// Viewtopic
//
$lang['View_topic'] = 'View topic';

$lang['Guest'] = 'Guest';
$lang['POST_SUBJECT'] = 'Post subject';
$lang['VIEW_NEXT_TOPIC'] = 'View next topic';
$lang['VIEW_PREVIOUS_TOPIC'] = 'View previous topic';
$lang['SUBMIT_VOTE'] = 'Submit Vote';
$lang['VIEW_RESULTS'] = 'View Results';

$lang['No_newer_topics'] = 'There are no newer topics in this forum';
$lang['No_older_topics'] = 'There are no older topics in this forum';
$lang['Topic_post_not_exist'] = 'The topic or post you requested does not exist';
$lang['No_posts_topic'] = 'No posts exist for this topic';

$lang['DISPLAY_POSTS'] = 'Display posts from previous';
$lang['All_Posts'] = 'All Posts';
$lang['Newest_First'] = 'Newest First';
$lang['Oldest_First'] = 'Oldest First';

$lang['BACK_TO_TOP'] = 'Back to top';

$lang['Read_profile'] = 'View user\'s profile';
$lang['Visit_website'] = 'Visit poster\'s website';
$lang['ICQ_status'] = 'ICQ Status';
$lang['View_IP'] = 'View IP address of poster';
$lang['Delete_post'] = 'Delete this post';

$lang['wrote'] = 'wrote'; // proceeds the username and is followed by the quoted text
$lang['Quote'] = 'Quote'; // comes before bbcode quote output.
$lang['Code'] = 'Code'; // comes before bbcode code output.
$lang['CODE_COPIED'] = 'Code copied to clipboard';
$lang['CODE_COPY'] = 'copy to clipboard';
$lang['Spoiler_head'] = 'hidden text';

$lang['Edited_time_total'] = 'Last edited by %s on %s; edited %d time in total'; // Last edited by me on 12 Oct 2001; edited 1 time in total
$lang['Edited_times_total'] = 'Last edited by %s on %s; edited %d times in total'; // Last edited by me on 12 Oct 2001; edited 2 times in total

$lang['LOCK_TOPIC'] = 'Lock this topic';
$lang['UNLOCK_TOPIC'] = 'Unlock this topic';
$lang['MOVE_TOPIC'] = 'Move this topic';
$lang['DELETE_TOPIC'] = 'Delete this topic';
$lang['SPLIT_TOPIC'] = 'Split this topic';

$lang['Stop_watching_topic'] = 'Stop watching this topic';
$lang['Start_watching_topic'] = 'Watch this topic for replies';
$lang['No_longer_watching'] = 'You are no longer watching this topic';
$lang['You_are_watching'] = 'You are now watching this topic';

$lang['TOTAL_VOTES'] = 'Total Votes';
$lang['SEARCH_IN_TOPIC'] = 'search in topic...';
$lang['HIDE_IN_TOPIC'] = 'Hide';

$lang['FLAGS'] = 'flags';
$lang['AVATARS'] = 'avatars';
$lang['RANK_IMAGES'] = 'rank images';
$lang['POST_IMAGES'] = 'post images';
$lang['SMILIES'] = 'smilies';
$lang['SIGNATURES'] = 'signatures';
$lang['SPOILER'] = 'Spoiler';
$lang['SHOW_OPENED'] = 'show opened';

$lang['MODERATE_TOPIC'] = 'Moderate this topic';
$lang['Select_posts_per_page'] = 'posts per page';

//
// Posting/Replying (Not private messaging!)
//
$lang['Message_body'] = 'Message body';
$lang['Topic_review'] = 'Topic review';

$lang['No_post_mode'] = 'No post mode specified'; // If posting.php is called without a mode (newtopic/reply/delete/etc, shouldn't be shown normaly)

$lang['Post_a_new_topic'] = 'Post a new topic';
$lang['Post_a_reply'] = 'Post a reply';
$lang['Post_topic_as'] = 'Post topic as';
$lang['Edit_post'] = 'Edit post';
$lang['OPTIONS'] = 'Options';

$lang['POST_ANNOUNCEMENT'] = 'Announcement';
$lang['POST_STICKY'] = 'Sticky';
$lang['POST_NORMAL'] = 'Normal';
$lang['POST_DOWNLOAD'] = 'Download';

$lang['Confirm_delete'] = 'Are you sure you want to delete this post?';
$lang['Confirm_delete_poll'] = 'Are you sure you want to delete this poll?';

$lang['Flood_Error'] = 'You cannot make another post so soon after your last; please try again in a short while.';
$lang['Empty_subject'] = 'You must specify a subject when posting a new topic.';
$lang['Empty_message'] = 'You must enter a message when posting.';
$lang['Forum_locked'] = 'This forum is locked: you cannot post, reply to, or edit topics.';
$lang['Topic_locked'] = 'This topic is locked: you cannot edit posts or make replies.';
$lang['Topic_locked_short'] = 'Topic locked';
$lang['No_post_id'] = 'You must select a post to edit';
$lang['No_topic_id'] = 'You must select a topic to reply to';
$lang['No_valid_mode'] = 'You can only post, reply, edit, or quote messages. Please return and try again.';
$lang['No_such_post'] = 'There is no such post. Please return and try again.';
$lang['Edit_own_posts'] = 'Sorry, but you can only edit your own posts.';
$lang['Delete_own_posts'] = 'Sorry, but you can only delete your own posts.';
$lang['Cannot_delete_replied'] = 'Sorry, but you may not delete posts that have been replied to.';
$lang['Cannot_delete_poll'] = 'Sorry, but you cannot delete an active poll.';
$lang['Empty_poll_title'] = 'You must enter a title for your poll.';
$lang['To_few_poll_options'] = 'You must enter at least two poll options.';
$lang['To_many_poll_options'] = 'You have tried to enter too many poll options.';
$lang['Post_has_no_poll'] = 'This post has no poll.';
$lang['Already_voted'] = 'You have already voted in this poll.';
$lang['No_vote_option'] = 'You must specify an option when voting.';
$lang['locked_warn'] = 'You posted into locked topic!';

$lang['Add_poll'] = 'Add a Poll';
$lang['Add_poll_explain'] = 'If you do not want to add a poll to your topic, leave the fields blank.';
$lang['Poll_question'] = 'Poll question';
$lang['Poll_option'] = 'Poll option';
$lang['Add_option'] = 'Add option';
$lang['UPDATE'] = 'Update';
$lang['DELETE'] = 'Delete';
$lang['Poll_for'] = 'Run poll for';
$lang['Days'] = 'Days';
$lang['Poll_for_explain'] = '[ Enter 0 or leave blank for a never-ending poll ]';
$lang['Delete_poll'] = 'Delete Poll';

$lang['Disable_BBCode_post'] = 'Disable BBCode in this post';
$lang['Disable_Smilies_post'] = 'Disable Smilies in this post';

$lang['BBCode_is_ON'] = '%sBBCode%s is <u>ON</u>'; // %s are replaced with URI pointing to FAQ
$lang['BBCode_is_OFF'] = '%sBBCode%s is <u>OFF</u>';
$lang['Smilies_are_ON'] = 'Smilies are <u>ON</u>';
$lang['Smilies_are_OFF'] = 'Smilies are <u>OFF</u>';

$lang['ATTACH_SIGNATURE'] = 'Attach signature (signatures can be changed in profile)';
$lang['Notify'] = 'Notify me when a reply is posted';

$lang['Stored'] = 'Your message has been entered successfully.';
$lang['Deleted'] = 'Your message has been deleted successfully.';
$lang['Poll_delete'] = 'Your poll has been deleted successfully.';
$lang['Vote_cast'] = 'Your vote has been cast.';

$lang['Topic_reply_notification'] = 'Topic Reply Notification';

$lang['bbcode_b_help'] = 'Bold text: [b]text[/b]  (alt+b)';
$lang['bbcode_i_help'] = 'Italic text: [i]text[/i]  (alt+i)';
$lang['bbcode_u_help'] = 'Underline text: [u]text[/u]  (alt+u)';
$lang['bbcode_q_help'] = 'Quote text: [quote]text[/quote]  (alt+q)';
$lang['bbcode_c_help'] = 'Code display: [code]code[/code]  (alt+c)';
$lang['bbcode_l_help'] = 'List: [list]text[/list] (alt+l)';
$lang['bbcode_o_help'] = 'Ordered list: [list=]text[/list]  (alt+o)';
$lang['bbcode_p_help'] = 'Insert image: [img]http://image_url[/img]  (alt+p)';
$lang['bbcode_w_help'] = 'Insert URL: [url]http://url[/url] or [url=http://url]URL text[/url]  (alt+w)';
$lang['bbcode_a_help'] = 'Close all open bbCode tags';
$lang['bbcode_s_help'] = 'Font color: [color=red]text[/color]  Tip: you can also use color=#FF0000';
$lang['bbcode_f_help'] = 'Font size: [size=x-small]small text[/size]';

$lang['Emoticons'] = 'Emoticons';
$lang['More_emoticons'] = 'View more Emoticons';

$lang['Font_color'] = 'Font colour';
$lang['color_default'] = 'Default';
$lang['color_dark_red'] = 'Dark Red';
$lang['color_red'] = 'Red';
$lang['color_orange'] = 'Orange';
$lang['color_brown'] = 'Brown';
$lang['color_yellow'] = 'Yellow';
$lang['color_green'] = 'Green';
$lang['color_olive'] = 'Olive';
$lang['color_cyan'] = 'Cyan';
$lang['color_blue'] = 'Blue';
$lang['color_dark_blue'] = 'Dark Blue';
$lang['color_indigo'] = 'Indigo';
$lang['color_violet'] = 'Violet';
$lang['color_white'] = 'White';
$lang['color_black'] = 'Black';

$lang['Font_size'] = 'Font size';
$lang['font_tiny'] = 'Tiny';
$lang['font_small'] = 'Small';
$lang['font_normal'] = 'Normal';
$lang['font_large'] = 'Large';
$lang['font_huge'] = 'Huge';

$lang['Close_Tags'] = 'Close Tags';
$lang['Styles_tip'] = 'Tip: Styles can be applied quickly to selected text.';

$lang['NEW_POSTS_PREVIEW'] = 'Topic has new, edited or unread posts';

//
// Private Messaging
//
$lang['Private_Messaging'] = 'Private Messaging';

$lang['No_new_pm'] = 'no new messages';

$lang['New_pms_format'] = '<b>%1$s</b> %2$s'; // 1 new message
$lang['New_pms_declension'] = array('new message', 'new messages');

$lang['Unread_pms_format'] = '<b>%1$s</b> %2$s'; // 1 new message
$lang['Unread_pms_declension'] = array('unread', 'unread');

$lang['Unread_message'] = 'Unread message';
$lang['Read_message'] = 'Read message';

$lang['Read_pm'] = 'Read message';
$lang['Post_new_pm'] = 'Post message';
$lang['Post_reply_pm'] = 'Reply to message';
$lang['Post_quote_pm'] = 'Quote message';
$lang['Edit_pm'] = 'Edit message';

$lang['Inbox'] = 'Inbox';
$lang['Outbox'] = 'Outbox';
$lang['Savebox'] = 'Savebox';
$lang['Sentbox'] = 'Sentbox';
$lang['Flag'] = 'Flag';
$lang['Subject'] = 'Subject';
$lang['FROM'] = 'From';
$lang['TO'] = 'To';
$lang['Date'] = 'Date';
$lang['Mark'] = 'Mark';
$lang['Sent'] = 'Sent';
$lang['Saved'] = 'Saved';
$lang['Delete_marked'] = 'Delete Marked';
$lang['Delete_all'] = 'Delete All';
$lang['Save_marked'] = 'Save Marked';
$lang['Save_message'] = 'Save Message';
$lang['Delete_message'] = 'Delete Message';

$lang['Display_messages'] = 'Display messages from previous'; // Followed by number of days/weeks/months
$lang['All_Messages'] = 'All Messages';

$lang['No_messages_folder'] = 'You have no messages in this folder';

$lang['PM_disabled'] = 'Private messaging has been disabled on this board.';
$lang['Cannot_send_privmsg'] = 'Sorry, but the administrator has prevented you from sending private messages.';
$lang['No_to_user'] = 'You must specify a username to whom to send this message.';
$lang['No_such_user'] = 'Sorry, but no such user exists.';

$lang['Disable_BBCode_pm'] = 'Disable BBCode in this message';
$lang['Disable_Smilies_pm'] = 'Disable Smilies in this message';

$lang['Message_sent'] = '<b>Your message has been sent.</b>';

$lang['Click_return_inbox'] = 'Return to your:<br /><br /> %s<b>Inbox</b>%s';
$lang['Click_return_sentbox'] = '&nbsp;&nbsp; %s<b>Sentbox</b>%s';
$lang['Click_return_outbox'] = '&nbsp;&nbsp; %s<b>Outbox</b>%s';
$lang['Click_return_savebox'] = '&nbsp;&nbsp; %s<b>Savebox</b>%s';
$lang['Click_return_index'] = '%sReturn to the Index%s';

$lang['Send_a_new_message'] = 'Send a new private message';
$lang['Send_a_reply'] = 'Reply to a private message';
$lang['Edit_message'] = 'Edit private message';

$lang['Notification_subject'] = 'New Private Message has arrived!';

$lang['FIND_USERNAME'] = 'Find a username';
$lang['SELECT_USERNAME'] = 'Select a Username';
$lang['Find'] = 'Find';
$lang['No_match'] = 'No matches found.';

$lang['No_post_id'] = 'No post ID was specified';
$lang['No_such_folder'] = 'No such folder exists';
$lang['No_folder'] = 'No folder specified';

$lang['Mark_all'] = 'Mark all';
$lang['Unmark_all'] = 'Unmark all';

$lang['Confirm_delete_pm'] = 'Are you sure you want to delete this message?';
$lang['Confirm_delete_pms'] = 'Are you sure you want to delete these messages?';

$lang['Inbox_size'] = 'Your Inbox is<br /><b>%d%%</b> full'; // eg. Your Inbox is 50% full
$lang['Sentbox_size'] = 'Your Sentbox is<br /><b>%d%%</b> full';
$lang['Savebox_size'] = 'Your Savebox is<br /><b>%d%%</b> full';

$lang['Click_view_privmsg'] = 'Click %sHere%s to visit your Inbox';

$lang['Outbox_expl'] = '';

//
// Profiles/Registration
//
$lang['Viewing_user_profile'] = 'Viewing profile :: %s'; // %s is username
$lang['About_user'] = 'All about %s'; // %s is username

$lang['Preferences'] = 'Preferences';
$lang['Items_required'] = 'Items marked with a * are required unless stated otherwise.';
$lang['Registration_info'] = 'Registration Information';
$lang['Profile_info'] = 'Profile Information';
$lang['Profile_info_warn'] = 'This information will be publicly viewable';
$lang['Avatar_panel'] = 'Avatar control panel';
$lang['Avatar_gallery'] = 'Avatar gallery';

$lang['WEBSITE'] = 'Website';
$lang['LOCATION'] = 'Location';
$lang['Contact'] = 'Contact';
$lang['Email_address'] = 'E-mail address';
$lang['Send_private_message'] = 'Send private message';
$lang['Hidden_email'] = '[ Hidden ]';
$lang['Interests'] = 'Interests';
$lang['Occupation'] = 'Occupation';
$lang['Poster_rank'] = 'Poster rank';

$lang['Total_posts'] = 'Total posts';
$lang['User_post_pct_stats'] = '%.2f%% of total'; // 1.25% of total
$lang['User_post_day_stats'] = '%.2f posts per day'; // 1.5 posts per day
$lang['Search_user_posts'] = 'Find posts by %s'; // Find all posts by username
$lang['Search_user_posts_short'] = 'Find user posts';

$lang['No_user_id_specified'] = 'Sorry, but that user does not exist.';
$lang['Wrong_Profile'] = 'You cannot modify a profile that is not your own.';

$lang['Only_one_avatar'] = 'Only one type of avatar can be specified';
$lang['File_no_data'] = 'The file at the URL you gave contains no data';
$lang['No_connection_URL'] = 'A connection could not be made to the URL you gave';
$lang['Incomplete_URL'] = 'The URL you entered is incomplete';
$lang['Wrong_remote_avatar_format'] = 'The URL of the remote avatar is not valid';
$lang['No_send_account_inactive'] = 'Sorry, but your password cannot be retrieved because your account is currently inactive';
$lang['No_send_account'] = 'Sorry, but your password cannot be retrieved. Please contact the forum administrator for more information';

$lang['Always_add_sig'] = 'Always attach my signature';
$lang['HIDE_PORN_FORUMS'] = 'Hide porno forums';
$lang['Always_notify'] = 'Always notify me of replies';
$lang['Always_notify_explain'] = 'Sends an e-mail when someone replies to a topic you have posted in. This can be changed whenever you post.';

$lang['Board_style'] = 'Board Style';
$lang['Board_lang'] = 'Board Language';
$lang['No_themes'] = 'No Themes In database';
$lang['Timezone'] = 'Timezone';
$lang['Date_format'] = 'Date format';
$lang['Date_format_explain'] = 'The syntax used is identical to the PHP <a href=\'http://www.php.net/date\' target=\'_other\'>date()</a> function.';
$lang['Signature'] = 'Signature';
$lang['Signature_explain'] = 'This is a block of text that can be added to posts you make. There is a %d character limit';
$lang['Public_view_email'] = 'Always show my e-mail address';

$lang['Current_password'] = 'Current password';
$lang['NEW_PASSWORD'] = 'New password';
$lang['Confirm_password'] = 'Confirm password';
$lang['Confirm_password_explain'] = 'You must confirm your current password if you wish to change it or alter your e-mail address';
$lang['password_if_changed'] = 'You only need to supply a password if you want to change it';
$lang['password_confirm_if_changed'] = 'You only need to confirm your password if you changed it above';

$lang['Autologin'] = 'Autologin';
$lang['Reset_autologin'] = 'Reset autologin key';
$lang['Reset_autologin_expl'] = '';

$lang['Avatar'] = 'Avatar';
$lang['Avatar_explain'] = 'Displays a small graphic image below your details in posts. Only one image can be displayed at a time, its width can be no greater than %d pixels, the height no greater than %d pixels, and the file size no more than %d KB.';
$lang['Upload_Avatar_file'] = 'Upload Avatar from your machine';
$lang['Upload_Avatar_URL'] = 'Upload Avatar from a URL';
$lang['Upload_Avatar_URL_explain'] = 'Enter the URL of the location containing the Avatar image, it will be copied to this site.';
$lang['Pick_local_Avatar'] = 'Select Avatar from the gallery';
$lang['Link_remote_Avatar'] = 'Link to off-site Avatar';
$lang['Link_remote_Avatar_explain'] = 'Enter the URL of the location containing the Avatar image you wish to link to.';
$lang['Avatar_URL'] = 'URL of Avatar Image';
$lang['Select_from_gallery'] = 'Select Avatar from gallery';
$lang['View_avatar_gallery'] = 'Show gallery';

$lang['Select_avatar'] = 'Select avatar';
$lang['Return_profile'] = 'Cancel avatar';
$lang['SELECT_CATEGORY'] = 'Select category';

$lang['Delete_Image'] = 'Delete Image';
$lang['Current_Image'] = 'Current Image';

$lang['Notify_on_privmsg'] = 'Notify on new Private Message';
$lang['Hide_user'] = 'Hide your online status';

$lang['Profile_updated'] = 'Your profile has been updated';
$lang['Profile_updated_inactive'] = 'Your profile has been updated. However, you have changed vital details, thus your account is now inactive. Check your e-mail to find out how to reactivate your account, or if admin activation is required, wait for the administrator to reactivate it.';

$lang['Password_mismatch'] = 'The passwords you entered did not match.';
$lang['Current_password_mismatch'] = 'The current password you supplied does not match that stored in the database.';
$lang['Password_long'] = 'Your password must be no more than 32 characters.';
$lang['Too_many_registers'] = 'You have made too many registration attempts. Please try again later.';
$lang['Username_taken'] = 'Sorry, but this username has already been taken.';
$lang['Username_invalid'] = 'Sorry, but this username contains an invalid character such as \'.';
$lang['Username_disallowed'] = 'Sorry, but this username has been disallowed.';
$lang['Email_taken'] = 'Sorry, but that e-mail address is already registered to a user.';
$lang['Email_banned'] = 'Sorry, but <b>%s</b> address has been banned.';
$lang['Email_invalid'] = 'Sorry, but this e-mail address is invalid.';
$lang['Signature_too_long'] = 'Your signature is too long.';
$lang['Fields_empty'] = 'You must fill in the required fields.';
$lang['Avatar_filetype'] = 'The avatar filetype must be .jpg, .gif or .png';
$lang['Avatar_filesize'] = 'The avatar image file size must be less than %d KB'; // The avatar image file size must be less than 6 KB
$lang['Avatar_imagesize'] = 'The avatar must be less than %d pixels wide and %d pixels high';

$lang['Welcome_subject'] = 'Welcome to %s Forums'; // Welcome to my.com forums
$lang['New_account_subject'] = 'New user account';
$lang['Account_activated_subject'] = 'Account Activated';

$lang['Account_added'] = 'Thank you for registering. Your account has been created. You may now log in with your username and password';
$lang['Account_inactive'] = 'Your account has been created. However, this forum requires account activation. An activation key has been sent to the e-mail address you provided. Please check your e-mail for further information';
$lang['Account_inactive_admin'] = 'Your account has been created. However, this forum requires account activation by the administrator. An e-mail has been sent to them and you will be informed when your account has been activated';
$lang['Account_active'] = 'Your account has now been activated. Thank you for registering';
$lang['Account_active_admin'] = 'The account has now been activated';
$lang['Reactivate'] = 'Reactivate your account!';
$lang['Already_activated'] = 'You have already activated your account';
$lang['COPPA'] = 'Your account has been created but has to be approved. Please check your e-mail for details.';

$lang['Registration'] = 'Registration Agreement Terms';

$lang['Agree_over_13'] = 'I Agree to these terms';
$lang['Agree_not'] = 'I do not agree to these terms';

$lang['Wrong_activation'] = 'The activation key you supplied does not match any in the database.';
$lang['Send_password'] = 'Send me a new password';
$lang['Password_updated'] = 'A new password has been created; please check your e-mail for details on how to activate it.';
$lang['No_email_match'] = 'The e-mail address you supplied does not match the one listed for that username.';
$lang['New_password_activation'] = 'New password activation';
$lang['Password_activated'] = 'Your account has been re-activated. To log in, please use the password supplied in the e-mail you received.';

$lang['Send_email_msg'] = 'Send an e-mail message';
$lang['No_user_specified'] = 'No user was specified';
$lang['User_prevent_email'] = 'This user does not wish to receive e-mail. Try sending them a private message.';
$lang['User_not_exist'] = 'That user does not exist';
$lang['CC_email'] = 'Send a copy of this e-mail to yourself';
$lang['Email_message_desc'] = 'This message will be sent as plain text, so do not include any HTML or BBCode. The return address for this message will be set to your e-mail address.';
$lang['Flood_email_limit'] = 'You cannot send another e-mail at this time. Try again later.';
$lang['Recipient'] = 'Recipient';
$lang['Email_sent'] = 'The e-mail has been sent.';
$lang['Send_email'] = 'Send e-mail';
$lang['Empty_subject_email'] = 'You must specify a subject for the e-mail.';
$lang['Empty_message_email'] = 'You must enter a message to be e-mailed.';

$lang['USER_AGREEMENT'] = 'User Agreement';
$lang['USER_AGREEMENT_HEAD'] = 'In order to proceed, you must agree with the following rules';
$lang['USER_AGREEMENT_AGREE'] = 'I have read and agree to the User Agreement above';

$lang['COPYRIGHT_HOLDERS'] = 'For Copyright Holders';
$lang['ADVERT'] = 'Advertise on this site';

//
// Visual confirmation system strings
//
$lang['Confirm_code_wrong'] = 'The confirmation code you entered was incorrect';
$lang['Too_many_registers'] = 'You have exceeded the number of registration attempts for this session. Please try again later.';
$lang['Confirm_code_impaired'] = 'If you are visually impaired or cannot otherwise read this code please contact the %sAdministrator%s for help.';
$lang['Confirm_code'] = 'Confirmation code';
$lang['Confirm_code_explain'] = 'Enter the code exactly as you see it. The code is case sensitive and zero has a diagonal line through it.';



//
// Memberslist
//
$lang['SORT'] = 'Sort';
$lang['Sort_Top_Ten'] = 'Top Ten Posters';
$lang['Sort_Joined'] = 'Joined Date';
$lang['Sort_Username'] = 'Username';
$lang['Sort_Location'] = 'Location';
$lang['Sort_Posts'] = 'Total posts';
$lang['Sort_Email'] = 'Email';
$lang['Sort_Website'] = 'Website';
$lang['ASC'] = 'Ascending';
$lang['DESC'] = 'Descending';
$lang['ORDER'] = 'Order';


//
// Group control panel
//
$lang['Group_Control_Panel'] = 'User Groups';
$lang['MEMBERSHIP_DETAILS'] = 'Group Membership Details';
$lang['JOIN_A_GROUP'] = 'Join a Group';

$lang['GROUP_INFORMATION'] = 'Group Information';
$lang['GROUP_NAME'] = 'Group name';
$lang['GROUP_DESCRIPTION'] = 'Group description';
$lang['GROUP_MEMBERSHIP'] = 'Group membership';
$lang['GROUP_MEMBERS'] = 'Group Members';
$lang['GROUP_MODERATOR'] = 'Group Moderator';
$lang['PENDING_MEMBERS'] = 'Pending Members';

$lang['GROUP_TYPE'] = 'Group type';
$lang['GROUP_OPEN'] = 'Open group';
$lang['GROUP_CLOSED'] = 'Closed group';
$lang['GROUP_HIDDEN'] = 'Hidden group';

$lang["Group_member_mod"] = 'Group moderator';
$lang["Group_member_member"] = 'Current memberships';
$lang["Group_member_pending"] = 'Memberships pending';
$lang["Group_member_open"] = 'Open groups';
$lang["Group_member_closed"] = 'Closed groups';
$lang["Group_member_hidden"] = 'Hidden groups';

$lang['No_groups_exist'] = 'No Groups Exist';
$lang['Group_not_exist'] = 'That user group does not exist';

$lang['NO_GROUP_MEMBERS'] = 'This group has no members';
$lang['HIDDEN_GROUP_MEMBERS'] = 'This group is hidden; you cannot view its membership';
$lang['No_pending_group_members'] = 'This group has no pending members';
$lang['Group_joined'] = 'You have successfully subscribed to this group.<br />You will be notified when your subscription is approved by the group moderator.';
$lang['Group_request'] = 'A request to join your group has been made.';
$lang['Group_approved'] = 'Your request has been approved.';
$lang['Group_added'] = 'You have been added to this usergroup.';
$lang['Already_member_group'] = 'You are already a member of this group';
$lang['User_is_member_group'] = 'User is already a member of this group';
$lang['Group_type_updated'] = 'Successfully updated group type.';

$lang['Could_not_add_user'] = 'The user you selected does not exist.';
$lang['Could_not_anon_user'] = 'You cannot make Anonymous a group member.';

$lang['Confirm_unsub'] = 'Are you sure you want to unsubscribe from this group?';
$lang['Confirm_unsub_pending'] = 'Your subscription to this group has not yet been approved; are you sure you want to unsubscribe?';

$lang['Unsub_success'] = 'You have been un-subscribed from this group.';

$lang['APPROVE_SELECTED'] = 'Approve Selected';
$lang['DENY_SELECTED'] = 'Deny Selected';
$lang['Not_logged_in'] = 'You must be logged in to join a group.';
$lang['REMOVE_SELECTED'] = 'Remove Selected';
$lang['ADD_MEMBER'] = 'Add Member';
$lang['Not_group_moderator'] = 'You are not this group\'s moderator, therefore you cannot perform that action.';

$lang['Login_to_join'] = 'Log in to join or manage group memberships';
$lang['This_open_group'] = 'This is an open group: click to request membership';
$lang['This_closed_group'] = 'This is a closed group: no more users accepted';
$lang['This_hidden_group'] = 'This is a hidden group: automatic user addition is not allowed';
$lang['Member_this_group'] = 'You are a member of this group';
$lang['Pending_this_group'] = 'Your membership of this group is pending';
$lang['Are_group_moderator'] = 'You are the group moderator';
$lang['None'] = 'None';

$lang['SUBSCRIBE'] = 'Subscribe';
$lang['UNSUBSCRIBE_GROUP'] = 'Unsubscribe';
$lang['VIEW_INFORMATION'] = 'View Information';


//
// Search
//
$lang['Search_query'] = 'Search Query';
$lang['SEARCH_OPTIONS'] = 'Search Options';

$lang['SEARCH_WORDS'] = 'Search for Keywords';
$lang['SEARCH_WORDS_EXPL'] = 'You can use <b>+</b> to define words which must be in the results and <b>-</b> to define words which should not be in the result (ex: "+word1 -word2"). Use * as a wildcard for partial matches';
$lang['SEARCH_AUTHOR'] = 'Search for Author';
$lang['SEARCH_AUTHOR_EXPL'] = 'Use * as a wildcard for partial matches';

$lang['Search_titles_only'] = 'Search topic titles only';
$lang['Search_all_words'] = 'all words';
$lang['IN_MY_POSTS']  = 'In my posts';
$lang['Search_my_topics'] = 'in my topics';
$lang['New_topics'] = 'New topics';

$lang['Return_first'] = 'Return first'; // followed by xxx characters in a select box
$lang['characters_posts'] = 'characters of posts';

$lang['SEARCH_PREVIOUS'] = 'Search previous';

$lang['SORT_BY'] = 'Sort by';
$lang['Sort_Time'] = 'Post Time';
$lang['Sort_Post_Subject'] = 'Post Subject';
$lang['Sort_Topic_Title'] = 'Topic Title';
$lang['Sort_Author'] = 'Author';
$lang['Sort_Forum'] = 'Forum';

$lang['DISPLAY_RESULTS_AS'] = 'Display results as';
$lang['All_available'] = 'All available';
$lang['Briefly'] = 'Briefly';
$lang['No_searchable_forums'] = 'You do not have permissions to search any forum on this site.';

$lang['No_search_match'] = 'No topics or posts met your search criteria';
$lang['Found_search_match'] = 'Search found %d match'; // eg. Search found 1 match
$lang['Found_search_matches'] = 'Search found %d matches'; // eg. Search found 24 matches
$lang['Too_many_search_results'] = 'Too many results may be found, please try to be more specific';

$lang['CLOSE_WINDOW'] = 'Close Window';
$lang['CLOSE'] = 'Close';
$lang['HIDE'] = 'hide';
$lang['SEARCH_TERMS'] = 'Search terms';

//
// Auth related entries
//
// Note the %s will be replaced with one of the following 'user' arrays
$lang['Sorry_auth_view'] = 'Sorry, but only %s can view this forum.';
$lang['Sorry_auth_read'] = 'Sorry, but only %s can read topics in this forum.';
$lang['Sorry_auth_post'] = 'Sorry, but only %s can post topics in this forum.';
$lang['Sorry_auth_reply'] = 'Sorry, but only %s can reply to posts in this forum.';
$lang['Sorry_auth_edit'] = 'Sorry, but only %s can edit posts in this forum.';
$lang['Sorry_auth_delete'] = 'Sorry, but only %s can delete posts in this forum.';
$lang['Sorry_auth_vote'] = 'Sorry, but only %s can vote in polls in this forum.';
$lang['Sorry_auth_sticky'] = 'Sorry, but only %s can post sticky messages in this forum.';
$lang['Sorry_auth_announce'] = 'Sorry, but only %s can post announcements in this forum.';

// These replace the %s in the above strings
$lang['Auth_Anonymous_Users'] = '<b>anonymous users</b>';
$lang['Auth_Registered_Users'] = '<b>registered users</b>';
$lang['Auth_Users_granted_access'] = '<b>users granted special access</b>';
$lang['Auth_Moderators'] = '<b>moderators</b>';
$lang['Auth_Administrators'] = '<b>administrators</b>';

$lang['Not_Moderator'] = 'You are not a moderator of this forum.';
$lang['Not_Authorised'] = 'Not Authorised';

$lang['You_been_banned'] = 'You have been banned from this forum.<br />Please contact the webmaster or board administrator for more information.';


//
// Viewonline
//
$lang['Reg_users_zero_online'] = 'There are 0 Registered users and '; // There are 5 Registered and
$lang['Reg_users_online'] = 'There are %d Registered users and '; // There are 5 Registered and
$lang['Reg_user_online'] = 'There is %d Registered user and '; // There is 1 Registered and
$lang['Hidden_users_zero_online'] = '0 Hidden users online'; // 6 Hidden users online
$lang['Hidden_users_online'] = '%d Hidden users online'; // 6 Hidden users online
$lang['Hidden_user_online'] = '%d Hidden user online'; // 6 Hidden users online
$lang['Guest_users_online'] = 'There are %d Guest users online'; // There are 10 Guest users online
$lang['Guest_users_zero_online'] = 'There are 0 Guest users online'; // There are 10 Guest users online
$lang['Guest_user_online'] = 'There is %d Guest user online'; // There is 1 Guest user online
$lang['No_users_browsing'] = 'There are no users currently browsing this forum';

$lang['ONLINE_EXPLAIN'] = 'users active over the past five minutes';

$lang['Last_updated'] = 'Last Updated';

$lang['Viewing_profile'] = 'Viewing profile';

//
// Moderator Control Panel
//
$lang['Mod_CP'] = 'Moderator Control Panel';
$lang['Mod_CP_explain'] = 'Using the form below you can perform mass moderation operations on this forum. You can lock, unlock, move or delete any number of topics.';

$lang['SELECT'] = 'Select';
$lang['DELETE'] = 'Delete';
$lang['MOVE'] = 'Move';
$lang['LOCK'] = 'Lock';
$lang['UNLOCK'] = 'Unlock';

$lang['Topics_Removed'] = 'The selected topics have been successfully removed from the database.';
$lang['Topics_Locked'] = 'The selected topics have been locked.';
$lang['Topics_Moved'] = 'The selected topics have been moved.';
$lang['Topics_Unlocked'] = 'The selected topics have been unlocked.';
$lang['No_Topics_Moved'] = 'No topics were moved.';

$lang['Confirm_delete_topic'] = 'Are you sure you want to remove the selected topic/s?';
$lang['Confirm_lock_topic'] = 'Are you sure you want to lock the selected topic/s?';
$lang['Confirm_unlock_topic'] = 'Are you sure you want to unlock the selected topic/s?';
$lang['Confirm_move_topic'] = 'Are you sure you want to move the selected topic/s?';

$lang['Move_to_forum'] = 'Move to forum';
$lang['Leave_shadow_topic'] = 'Leave shadow topic in old forum.';

$lang['Split_Topic'] = 'Split Topic Control Panel';
$lang['Split_Topic_explain'] = 'Using the form below you can split a topic in two, either by selecting the posts individually or by splitting at a selected post';
$lang['NEW_TOPIC_TITLE'] = 'New topic title';
$lang['FORUM_FOR_NEW_TOPIC'] = 'Forum for new topic';
$lang['SPLIT_POSTS'] = 'Split selected posts';
$lang['SPLIT_AFTER'] = 'Split from selected post';
$lang['Topic_split'] = 'The selected topic has been split successfully';

$lang['Too_many_error'] = 'You have selected too many posts. You can only select one post to split a topic after!';

$lang['None_selected'] = 'You have none selected to perform this operation on. Please go back and select at least one.';
$lang['New_forum'] = 'New forum';

$lang['This_posts_IP'] = 'IP address for this post';
$lang['Other_IP_this_user'] = 'Other IP addresses this user has posted from';
$lang['Users_this_IP'] = 'Users posting from this IP address';
$lang['IP_info'] = 'IP Information';
$lang['Lookup_IP'] = 'Look up IP address';


//
// Timezones ... for display on each page
//
$lang['All_times'] = 'All times are <span class="tz_time">%s</span>'; // eg. All times are GMT - 12 Hours (times from next block)

$lang['-12'] = 'GMT - 12 Hours';
$lang['-11'] = 'GMT - 11 Hours';
$lang['-10'] = 'GMT - 10 Hours';
$lang['-9'] = 'GMT - 9 Hours';
$lang['-8'] = 'GMT - 8 Hours';
$lang['-7'] = 'GMT - 7 Hours';
$lang['-6'] = 'GMT - 6 Hours';
$lang['-5'] = 'GMT - 5 Hours';
$lang['-4'] = 'GMT - 4 Hours';
$lang['-3.5'] = 'GMT - 3.5 Hours';
$lang['-3'] = 'GMT - 3 Hours';
$lang['-2'] = 'GMT - 2 Hours';
$lang['-1'] = 'GMT - 1 Hours';
$lang['0'] = 'GMT';
$lang['1'] = 'GMT + 1 Hour';
$lang['2'] = 'GMT + 2 Hours';
$lang['3'] = 'GMT + 3 Hours';
$lang['3.5'] = 'GMT + 3.5 Hours';
$lang['4'] = 'GMT + 4 Hours';
$lang['4.5'] = 'GMT + 4.5 Hours';
$lang['5'] = 'GMT + 5 Hours';
$lang['5.5'] = 'GMT + 5.5 Hours';
$lang['6'] = 'GMT + 6 Hours';
$lang['6.5'] = 'GMT + 6.5 Hours';
$lang['7'] = 'GMT + 7 Hours';
$lang['8'] = 'GMT + 8 Hours';
$lang['9'] = 'GMT + 9 Hours';
$lang['9.5'] = 'GMT + 9.5 Hours';
$lang['10'] = 'GMT + 10 Hours';
$lang['11'] = 'GMT + 11 Hours';
$lang['12'] = 'GMT + 12 Hours';
$lang['13'] = 'GMT + 13 Hours';

// These are displayed in the timezone select box
$lang['tz']['-12'] = 'GMT - 12 Hours';
$lang['tz']['-11'] = 'GMT - 11 Hours';
$lang['tz']['-10'] = 'GMT - 10 Hours';
$lang['tz']['-9'] = 'GMT - 9 Hours';
$lang['tz']['-8'] = 'GMT - 8 Hours';
$lang['tz']['-7'] = 'GMT - 7 Hours';
$lang['tz']['-6'] = 'GMT - 6 Hours';
$lang['tz']['-5'] = 'GMT - 5 Hours';
$lang['tz']['-4'] = 'GMT - 4 Hours';
$lang['tz']['-3.5'] = 'GMT - 3.5 Hours';
$lang['tz']['-3'] = 'GMT - 3 Hours';
$lang['tz']['-2'] = 'GMT - 2 Hours';
$lang['tz']['-1'] = 'GMT - 1 Hours';
$lang['tz']['0'] = 'GMT';
$lang['tz']['1'] = 'GMT + 1 Hour';
$lang['tz']['2'] = 'GMT + 2 Hours';
$lang['tz']['3'] = 'GMT + 3 Hours';
$lang['tz']['3.5'] = 'GMT + 3.5 Hours';
$lang['tz']['4'] = 'GMT + 4 Hours';
$lang['tz']['4.5'] = 'GMT + 4.5 Hours';
$lang['tz']['5'] = 'GMT + 5 Hours';
$lang['tz']['5.5'] = 'GMT + 5.5 Hours';
$lang['tz']['6'] = 'GMT + 6 Hours';
$lang['tz']['6.5'] = 'GMT + 6.5 Hours';
$lang['tz']['7'] = 'GMT + 7 Hours';
$lang['tz']['8'] = 'GMT + 8 Hours';
$lang['tz']['9'] = 'GMT + 9 Hours';
$lang['tz']['9.5'] = 'GMT + 9.5 Hours';
$lang['tz']['10'] = 'GMT + 10 Hours';
$lang['tz']['11'] = 'GMT + 11 Hours';
$lang['tz']['12'] = 'GMT + 12 Hours';
$lang['tz']['13'] = 'GMT + 13 Hours';

$lang['datetime']['Sunday'] = 'Sunday';
$lang['datetime']['Monday'] = 'Monday';
$lang['datetime']['Tuesday'] = 'Tuesday';
$lang['datetime']['Wednesday'] = 'Wednesday';
$lang['datetime']['Thursday'] = 'Thursday';
$lang['datetime']['Friday'] = 'Friday';
$lang['datetime']['Saturday'] = 'Saturday';
$lang['datetime']['Sun'] = 'Sun';
$lang['datetime']['Mon'] = 'Mon';
$lang['datetime']['Tue'] = 'Tue';
$lang['datetime']['Wed'] = 'Wed';
$lang['datetime']['Thu'] = 'Thu';
$lang['datetime']['Fri'] = 'Fri';
$lang['datetime']['Sat'] = 'Sat';
$lang['datetime']['January'] = 'January';
$lang['datetime']['February'] = 'February';
$lang['datetime']['March'] = 'March';
$lang['datetime']['April'] = 'April';
$lang['datetime']['May'] = 'May';
$lang['datetime']['June'] = 'June';
$lang['datetime']['July'] = 'July';
$lang['datetime']['August'] = 'August';
$lang['datetime']['September'] = 'September';
$lang['datetime']['October'] = 'October';
$lang['datetime']['November'] = 'November';
$lang['datetime']['December'] = 'December';
$lang['datetime']['Jan'] = 'Jan';
$lang['datetime']['Feb'] = 'Feb';
$lang['datetime']['Mar'] = 'Mar';
$lang['datetime']['Apr'] = 'Apr';
$lang['datetime']['May'] = 'May';
$lang['datetime']['Jun'] = 'Jun';
$lang['datetime']['Jul'] = 'Jul';
$lang['datetime']['Aug'] = 'Aug';
$lang['datetime']['Sep'] = 'Sep';
$lang['datetime']['Oct'] = 'Oct';
$lang['datetime']['Nov'] = 'Nov';
$lang['datetime']['Dec'] = 'Dec';

//
// Errors (not related to a
// specific failure on a page)
//
$lang['INFORMATION'] = 'Information';
$lang['Critical_Information'] = 'Critical Information';

$lang['General_Error'] = 'Error';
$lang['Critical_Error'] = 'Critical Error';
$lang['An_error_occured'] = 'An Error Occurred';
$lang['A_critical_error'] = 'A Critical Error Occurred';

$lang['Admin_reauthenticate'] = 'To administer/moderate the board you must re-authenticate yourself.';
$lang['Login_attempts_exceeded'] = 'The maximum number of %s login attempts has been exceeded. You are not allowed to login for the next %s minutes.';
$lang['Please_remove_install_contrib'] = 'Please ensure both the install/ and contrib/ directories are deleted';

//
// Attachment Mod Main Language Variables
//

// Auth Related Entries
$lang['Rules_attach_can'] = 'You <b>can</b> attach files in this forum';
$lang['Rules_attach_cannot'] = 'You <b>cannot</b> attach files in this forum';
$lang['Rules_download_can'] = 'You <b>can</b> download files in this forum';
$lang['Rules_download_cannot'] = 'You <b>cannot</b> download files in this forum';
$lang['Sorry_auth_view_attach'] = 'Sorry but you are not authorized to view or download this Attachment';

// Viewtopic -> Display of Attachments
$lang['Description'] = 'Description'; // used in Administration Panel too...
$lang['Download'] = 'Download'; // this Language Variable is defined in lang_admin.php too, but we are unable to access it from the main Language File
$lang['Filesize'] = 'Filesize';
$lang['VIEWED'] = 'Viewed';
$lang['Download_number'] = '%d times'; // replace %d with count
$lang['Extension_disabled_after_posting'] = 'The Extension \'%s\' was deactivated by an board admin, therefore this Attachment is not displayed.'; // used in Posts and PM's, replace %s with mime type

// Posting/PM -> Posting Attachments
$lang['Add_attachment'] = 'Add Attachment';
$lang['Add_attachment_title'] = 'Add an Attachment';
$lang['Add_attachment_explain'] = 'If you do not want to add an Attachment to your Post, please leave the Fields blank';
$lang['File_name'] = 'Filename';
$lang['File_comment'] = 'File Comment';

// Posting/PM -> Posted Attachments
$lang['Posted_attachments'] = 'Posted Attachments';
$lang['Update_comment'] = 'Update Comment';
$lang['Delete_attachments'] = 'Delete Attachments';
$lang['Delete_attachment'] = 'Delete Attachment';
$lang['Delete_thumbnail'] = 'Delete Thumbnail';
$lang['Upload_new_version'] = 'Upload New Version';

// Errors -> Posting Attachments
$lang['Invalid_filename'] = '%s is an invalid filename'; // replace %s with given filename
$lang['Attachment_php_size_na'] = 'The Attachment is too big.<br />Couldn\'t get the maximum Size defined in PHP.<br />The Attachment Mod is unable to determine the maximum Upload Size defined in the php.ini.';
$lang['Attachment_php_size_overrun'] = 'The Attachment is too big.<br />Maximum Upload Size: %d MB.<br />Please note that this Size is defined in php.ini, this means it\'s set by PHP and the Attachment Mod can not override this value.'; // replace %d with ini_get('upload_max_filesize')
$lang['Disallowed_extension'] = 'The Extension %s is not allowed'; // replace %s with extension (e.g. .php)
$lang['Disallowed_extension_within_forum'] = 'You are not allowed to post Files with the Extension %s within this Forum'; // replace %s with the Extension
$lang['Attachment_too_big'] = 'The Attachment is too big.<br />Max Size: %d %s'; // replace %d with maximum file size, %s with size var
$lang['Attach_quota_reached'] = 'Sorry, but the maximum filesize for all Attachments is reached. Please contact the Board Administrator if you have questions.';
$lang['Too_many_attachments'] = 'Attachment cannot be added, since the max. number of %d Attachments in this post was achieved'; // replace %d with maximum number of attachments
$lang['Error_imagesize'] = 'The Attachment/Image must be less than %d pixels wide and %d pixels high';
$lang['General_upload_error'] = 'Upload Error: Could not upload Attachment to %s.'; // replace %s with local path

$lang['Error_empty_add_attachbox'] = 'You have to enter values in the \'Add an Attachment\' Box';
$lang['Error_missing_old_entry'] = 'Unable to Update Attachment, could not find old Attachment Entry';

// Errors -> PM Related
$lang['Attach_quota_sender_pm_reached'] = 'Sorry, but the maximum filesize for all Attachments in your Private Message Folder has been reached. Please delete some of your received/sent Attachments.';
$lang['Attach_quota_receiver_pm_reached'] = 'Sorry, but the maximum filesize for all Attachments in the Private Message Folder of \'%s\' has been reached. Please let him know, or wait until he/she has deleted some of his/her Attachments.';

// Errors -> Download
$lang['No_attachment_selected'] = 'You haven\'t selected an attachment to download or view.';
$lang['Error_no_attachment'] = 'The selected Attachment does not exist anymore';

// Delete Attachments
$lang['Confirm_delete_attachments'] = 'Are you sure you want to delete the selected Attachments?';
$lang['Deleted_attachments'] = 'The selected Attachments have been deleted.';
$lang['Error_deleted_attachments'] = 'Could not delete Attachments.';
$lang['Confirm_delete_pm_attachments'] = 'Are you sure you want to delete all Attachments posted in this PM?';

// General Error Messages
$lang['Attachment_feature_disabled'] = 'The Attachment Feature is disabled.';

$lang['Directory_does_not_exist'] = 'The Directory \'%s\' does not exist or couldn\'t be found.'; // replace %s with directory
$lang['Directory_is_not_a_dir'] = 'Please check if \'%s\' is a directory.'; // replace %s with directory
$lang['Directory_not_writeable'] = 'Directory \'%s\' is not writeable. You\'ll have to create the upload path and chmod it to 777 (or change the owner to you httpd-servers owner) to upload files.<br />If you have only plain ftp-access change the \'Attribute\' of the directory to rwxrwxrwx.'; // replace %s with directory

$lang['Ftp_error_connect'] = 'Could not connect to FTP Server: \'%s\'. Please check your FTP-Settings.';
$lang['Ftp_error_login'] = 'Could not login to FTP Server. The Username \'%s\' or the Password is wrong. Please check your FTP-Settings.';
$lang['Ftp_error_path'] = 'Could not access ftp directory: \'%s\'. Please check your FTP Settings.';
$lang['Ftp_error_upload'] = 'Could not upload files to ftp directory: \'%s\'. Please check your FTP Settings.';
$lang['Ftp_error_delete'] = 'Could not delete files in ftp directory: \'%s\'. Please check your FTP Settings.<br />Another reason for this error could be the non-existence of the Attachment, please check this first in Shadow Attachments.';
$lang['Ftp_error_pasv_mode'] = 'Unable to enable/disable FTP Passive Mode';

// Attach Rules Window
$lang['Rules_page'] = 'Attachment Rules';
$lang['Attach_rules_title'] = 'Allowed Extension Groups and their Sizes';
$lang['Group_rule_header'] = '%s -> Maximum Upload Size: %s'; // Replace first %s with Extension Group, second one with the Size STRING
$lang['Allowed_extensions_and_sizes'] = 'Allowed Extensions and Sizes';
$lang['Note_user_empty_group_permissions'] = 'NOTE:<br />You are normally allowed to attach files within this Forum, <br />but since no Extension Group is allowed to be attached here, <br />you are unable to attach anything. If you try, <br />you will receive an Error Message.<br />';

// Quota Variables
$lang['UPLOAD_QUOTA'] = 'Upload Quota';
$lang['PM_QUOTA'] = 'PM Quota';
$lang['User_upload_quota_reached'] = 'Sorry, you have reached your maximum Upload Quota Limit of %d %s'; // replace %d with Size, %s with Size Lang (MB for example)

// User Attachment Control Panel
$lang['User_acp_title'] = 'User ACP';
$lang['UACP'] = 'User Attachment Control Panel';
$lang['User_uploaded_profile'] = 'Uploaded: %s';
$lang['User_quota_profile'] = 'Quota: %s';
$lang['Upload_percent_profile'] = '%d%% of total';

// Common Variables
$lang['Bytes'] = 'Bytes';
$lang['KB'] = 'KB';
$lang['MB'] = 'MB';
$lang['Attach_search_query'] = 'Search Attachments';
$lang['Test_settings'] = 'Test Settings';
$lang['Not_assigned'] = 'Not Assigned';
$lang['No_file_comment_available'] = 'No File Comment available';
$lang['Attachbox_limit'] = 'Your Attachbox is<br /><b>%d%%</b> full';
$lang['No_quota_limit'] = 'No Quota Limit';
$lang['Unlimited'] = 'Unlimited';

//bt
$lang['Bt_Reg_YES'] = 'Registered';
$lang['Bt_Reg_NO'] = 'Not registered';
$lang['Bt_Added'] = 'Added';
$lang['Bt_Reg_on_tracker'] = 'Register on tracker';
$lang['Bt_Reg_fail'] = 'Could not register torrent on tracker';
$lang['Bt_Reg_fail_same_hash'] = 'Another torrent with same info_hash already <a href="%s"><b>registered</b></a>';
$lang['Bt_Unreg_from_tracker'] = 'Remove from tracker';
$lang['Bt_Deleted'] = 'Torrent removed from tracker';
$lang['Bt_Registered'] = 'Torrent registered on tracker<br /><br />Now you need to <a href="%s"><b>download your torrent</b></a> and run it using your BitTorrent client choosing the folder with the original files you\'re sharing as the download path';
$lang['Invalid_ann_url'] = 'Invalid Announce URL [%s]<br /><br />must be <b>%s</b>';
$lang['Passkey_err_tor_not_reg'] = 'Could not add passkey<br /><br />Torrent not registered on tracker';
$lang['Passkey_err_empty'] = 'Could not add passkey (passkey is empty)<br /><br />Go to <a href="%s" target="_blank"><b>your forum profile</b></a> and generate it';
$lang['Bt_Gen_Passkey'] = 'Passkey';
$lang['Bt_Gen_Passkey_Url'] = 'Generate or change Passkey';
$lang['Bt_Gen_Passkey_Explain'] = 'Generate your personal id for torrent tracker';
$lang['Bt_Gen_Passkey_Explain_2'] = "<b>Warning!</b> After generating new id you'll need to <b>redownload all active torrent's!</b>";
$lang['Bt_Gen_Passkey_OK'] = 'New personal identifier generated';
$lang['Bt_No_searchable_forums'] = 'No searchable forums found';

$lang['SEEDERS'] = 'Seeders';
$lang['LEECHERS'] = 'Leechers';
$lang['Seeding'] = 'Seeding';
$lang['Leeching'] = 'Leeching';
$lang['IS_REGISTERED'] = 'Registered';

//torrent status mod
$lang['TOR_STATUS'] = 'Status';
$lang['TOR_STATUS_SELECT_ACTION'] = 'Select status';
$lang['TOR_STATUS_CHECKED'] = 'checked';//2
$lang['TOR_STATUS_NOT_CHECKED'] = 'not checked';//0
$lang['TOR_STATUS_CLOSED'] = 'closed';//1
$lang['TOR_STATUS_D'] = 'repeat';//3
$lang['TOR_STATUS_NOT_PERFECT'] = 'neoformleno';//4
$lang['TOR_STATUS_PART_PERFECT'] = 'nedooformleno';//5
$lang['TOR_STATUS_FISHILY'] = 'doubtful';//6
$lang['TOR_STATUS_COPY'] = 'closed right';//7
//end torrent status mod

$lang['Bt_Topic_Title'] = 'Topic title';
$lang['Bt_Seeder_last_seen'] = 'Seed last seen';
$lang['Bt_Sort_Forum'] = 'Forum';
$lang['SIZE'] = 'Size';
$lang['PIECE_LENGTH'] = 'Piece length';
$lang['COMPLETED'] = 'Completed';
$lang['ADDED'] = 'Added';
$lang['DELETE_TORRENT'] = 'Delete torrent';
$lang['DEL_MOVE_TORRENT'] = 'Delete and move topic';
$lang['DL_TORRENT'] = 'Download .torrent';
$lang['Bt_Last_post'] = 'Last post';
$lang['Bt_Created'] = 'Topic posted';
$lang['Bt_Replies'] = 'Replies';
$lang['Bt_Views'] = 'Views';
$lang['FREEZE_TORRENT'] = 'Disable downloading';
$lang['UNFREEZE_TORRENT'] = 'Allow downloading';

$lang['SEARCH_IN_FORUMS'] = 'Search in Forums';
$lang['SELECT_CAT'] = 'Select category';
$lang['GO_TO_SECTION'] = 'Goto section';
$lang['TORRENTS_FROM'] = 'Posts from';
$lang['SHOW_ONLY'] = 'Show only';
$lang['SHOW_COLUMN'] = 'Show column';

$lang['Bt_Only_Active'] = 'Active';
$lang['Bt_Only_My'] = 'My releases';
$lang['Bt_Seed_exist'] = 'Seeder exist';
$lang['Bt_Only_New'] = 'New from last visit';
$lang['Bt_Show_Cat'] = 'Category';
$lang['Bt_Show_Forum'] = 'Forum';
$lang['Bt_Show_Author'] = 'Author';
$lang['Bt_Show_Speed'] = 'Speed';
$lang['SEED_NOT_SEEN'] = 'Seeder not seen';
$lang['TITLE_MATCH'] = 'Title match';
$lang['Bt_User_not_found'] = 'not found';
$lang['DL_SPEED'] = 'Overall download speed';

$lang['Bt_Disregard'] = 'disregarding';
$lang['Bt_Never'] = 'never';
$lang['Bt_All_Days_for'] = 'all the time';
$lang['Bt_1_Day_for'] = 'last day';
$lang['Bt_3_Day_for'] = 'last three days';
$lang['Bt_7_Days_for'] = 'last week';
$lang['Bt_2_Weeks_for'] = 'last two weeks';
$lang['Bt_1_Month_for'] = 'last month';
$lang['Bt_1_Day']    = '1 day';
$lang['Bt_3_Days']    = '3 days';
$lang['Bt_7_Days']   = 'week';
$lang['Bt_2_Weeks']  = '2 weeks';
$lang['Bt_1_Month']  = 'month';

$lang['DL_LIST_AND_TORRENT_ACTIVITY'] = 'Torrent stats';
$lang['DL_WILL'] = 'Will download';
$lang['DL_DOWN'] = 'Downloading';
$lang['DL_COMPLETE'] = 'Complete';
$lang['DL_CANCEL'] = 'Cancel';

$lang['dlWill_2'] = 'Will download';
$lang['dlDown_2'] = 'Downloading';
$lang['dlComplete_2'] = 'Complete';
$lang['dlCancel_2'] = 'Cancel';

$lang['DL_List_Del'] = 'Clear DL-List';
$lang['DL_List_Del_Confirm'] = 'Delete DL-List for this topic?';
$lang['SHOW_DL_LIST'] = 'Show DL-List';
$lang['Set_DL_Status'] = 'Download';
$lang['Unset_DL_Status'] = 'Not Download';
$lang['Topics_Down_Sets'] = 'Topic status changed to <b>Download</b>';
$lang['Topics_Down_Unsets'] = '<b>Download</b> status removed';

$lang['Topic_DL'] = 'DL';

$lang['MY_RATING_AND_DLS'] = 'Rating/Downloads';
$lang['MY_DOWNLOADS'] = 'My Downloads';
$lang['SEARCH_DL_WILL'] = 'Planned';
$lang['SEARCH_DL_WILL_DOWNLOADS'] = 'Planned Downloads';
$lang['SEARCH_DL_DOWN'] = 'Current';
$lang['SEARCH_DL_COMPLETE'] = 'Completed';
$lang['SEARCH_DL_COMPLETE_DOWNLOADS']   = 'Completed Downloads';
$lang['SEARCH_DL_CANCEL'] = 'Canceled';
$lang['CUR_DOWNLOADS'] = 'Current Downloads';
$lang['CUR_UPLOADS']   = 'Current Uploads';
$lang['Search_user_releases'] = 'Find all current releases';
$lang['TOR_SEARCH_TITLE'] = 'Torrent search options';
$lang['OPEN_TOPIC'] = 'Open topic';

$lang['Allowed_only_1st_post_attach'] = 'Posting torrents allowed only in first post';
$lang['Allowed_only_1st_post_reg'] = 'Registering torrents allowed only from first post';
$lang['Reg_not_allowed_in_this_forum'] = 'Could not register torrent in this forum';
$lang['Already_reg'] = 'Torrent already registered';
$lang['Not_torrent'] = 'This file is not torrent';
$lang['Only_1_tor_per_post'] = 'You can register only one torrent in one post';
$lang['Only_1_tor_per_topic'] = 'You can register only one torrent in one topic';
$lang['Viewing_user_bt_profile'] = 'Viewing torrent-profile :: %s'; // %s is username
$lang['Cur_active_dls'] = 'Currently active torrents';
$lang['View_torrent_profile'] = 'Torrent-profile';
$lang['Curr_passkey'] = 'Current passkey:';
$lang['SPMODE_FULL'] = 'Show peers in full details';

$lang['BT_RATIO'] = 'Ratio';
$lang['YOUR_RATIO'] = 'Your Ratio';
$lang['DOWNLOADED'] = 'Downloaded';
$lang['UPLOADED'] = 'Uploaded';
$lang['RELEASED'] = 'Released';
$lang['BT_BONUS_UP'] = 'Bonus';

$lang['TRACKER'] = 'Tracker';
$lang['OPEN_TOPICS'] = 'Open topics';
$lang['OPEN_IN_SAME_WINDOW'] = 'open in same window';

$lang['Bt_Low_ratio_func'] = "You can't use this option (ratio is too low)";
$lang['Bt_Low_ratio_for_dl'] = "With ratio <b>%s</b> you can't download torrents";
$lang['bt_ratio_warning_msg'] = 'If your rating falls below %s, you will not be able to download Torrents! <a href="%s"><b>More about the rating.</b></a>';

$lang['Seeder_last_seen'] = 'Seeder not seen: <b>%s</b>';

//
// MAIL.RU Keyboard
//
$lang['kb_title'] = 'Russian keyboard';
$lang['kb_rus_keylayout'] = 'Layout: ';
$lang['kb_none'] = 'None';
$lang['kb_translit'] = 'Translit';
$lang['kb_traditional'] = 'Traditional';
$lang['kb_rules'] = 'Using translit';
$lang['kb_show'] = 'Show keyboard (Make sure you\'re using Cyrillic codepage!)';
$lang['kb_about'] = 'About';
$lang['kb_close'] = 'Close';
$lang['kb_translit_mozilla'] = 'Select text you wish to translit and click \'Translit\'.';
$lang['kb_translit_opera7'] = 'Click here to translit your message.';

$lang['Need_to_login_first'] = 'You need to login first';
$lang['Only_for_mod'] = 'This option only for moderators';
$lang['Only_for_admin'] = 'This option only for admins';
$lang['Only_for_super_admin'] = 'This option only for super admins';

$lang['Access'] = 'Access';
$lang['Access_srv_load'] = 'Depend on server load';

//
// That's all, Folks!
// -------------------------------------------------

// from lang_admin
$lang['Not_admin'] = 'You are not authorised to administer this board';

$lang['COOKIES_REQUIRED'] = 'Cookies must be enabled!';
$lang['Session_Expired'] = 'Session expired';

// FLAGHACK-start
$lang['Country_Flag'] = 'Country Flag';
$lang['Select_Country'] = 'SELECT COUNTRY' ;
// FLAGHACK-end

// Sort memberlist per letter
$lang['Sort_per_letter'] = 'Show only usernames starting with';
$lang['Others'] = 'others';
$lang['All'] = 'all';

$lang['POST_LINK'] = 'Post link';
$lang['LAST_VISITED'] = 'Last Visited';
$lang['LAST_ACTIVITY'] = 'Last activity';
$lang['Never'] = 'Never';

//mpd
$lang['DELETE_POSTS'] = 'Delete selected posts';
$lang['Delete_posts_succesfully'] = 'The selected posts have been successfully removed';
//mpd end

//ts
$lang['Topics_Announcement'] = 'Announcements';
$lang['Topics_Sticky'] = 'Stickies';
$lang['Topics_Normal'] = 'Topics';
//ts end

//dpc
$lang['Double_Post_Error'] = 'You cannot make another post with the exact same text as your last.';
//dpc end

//upt
$lang['Update_post_time'] = 'Update post time';
//upt end

$lang['Topic_split_new'] = 'New topic';
$lang['Topic_split_old'] = 'Old topic';
$lang['Bot_leave_msg_moved'] = 'Add bot-message about moving';
$lang['BOT_AFTER_SPLIT_TO_OLD'] = 'Add bot-message about split to <b>old topic</b>';
$lang['BOT_AFTER_SPLIT_TO_NEW'] = 'Add bot-message about split to <b>new topic</b>';
//qr
$lang['QUICK_REPLY'] = 'Quick Reply';
$lang['INS_NAME_TIP'] = 'Insert name or selected text.';
$lang['QUOTE_SELECTED'] = 'Quote selected';
$lang['TRANSLIT_RULES'] = 'Translit Rules';
$lang['QR_ATTACHSIG'] = 'Attach signature';
$lang['QR_NOTIFY'] = 'Notify on reply';
$lang['QR_DISABLE'] = 'Disable';
$lang['QR_USERNAME'] = 'Name';
$lang['No_Text_Sel'] = 'Select a text anywhere on a page and try again';
$lang['QR_Font_sel'] = 'Font face';
$lang['QR_Color_sel'] = 'Font color';
$lang['QR_Size_sel'] = 'Font size';
$lang['color_steel_blue'] = 'Steel Blue';
$lang['color_gray'] = 'Gray';
$lang['color_dark_green'] = 'Dark Green';
//qr end

//txtb
$lang['ICQ_txtb'] = '[ICQ]';
$lang['AIM_txtb'] = '[AIM]';
$lang['MSNM_txtb'] = '[MSN]';
$lang['YIM_txtb'] = '[Yahoo]';
$lang['Reply_with_quote_txtb'] = '[Quote]';
$lang['Read_profile_txtb'] = '[Profile]';
$lang['Send_email_txtb'] = '[E-mail]';
$lang['Visit_website_txtb'] = '[www]';
$lang['Edit_delete_post_txtb'] = '[Edit]';
$lang['Search_user_posts_txtb'] = '[Search]';
$lang['View_IP_txtb'] = '[ip]';
$lang['Delete_post_txtb'] = '[x]';
$lang['Send_pm_txtb'] = '[PM]';
//txtb end

$lang['declension']['replies'] = array('reply', 'replies');
$lang['declension']['times'] = array('time', 'times');

$lang['delta_time']['intervals'] = array(
	'seconds' => array('second', 'seconds'),
	'minutes' => array('minute', 'minutes'),
	'hours'   => array('hour',   'hours'),
	'mday'    => array('day',    'days'),
	'mon'     => array('month',  'months'),
	'year'    => array('year',   'years'),
);
$lang['delta_time']['format'] = '%1$s %2$s';  // 5(%1) minutes(%2)

$lang['auth_types'][AUTH_ALL]   = $lang['Auth_Anonymous_Users'];
$lang['auth_types'][AUTH_REG]   = $lang['Auth_Registered_Users'];
$lang['auth_types'][AUTH_ACL]   = $lang['Auth_Users_granted_access'];
$lang['auth_types'][AUTH_MOD]   = $lang['Auth_Moderators'];
$lang['auth_types'][AUTH_ADMIN] = $lang['Auth_Administrators'];

$lang['new_user_reg_disabled'] = 'Sorry, registration is disabled at this time';
$lang['ONLY_NEW_POSTS'] = 'only new posts';
$lang['ONLY_NEW_TOPICS'] = 'only new topics';

$lang['TORHELP_TITLE'] = 'Please help seeding these torrents!';
