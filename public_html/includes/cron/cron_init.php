<?php

if (!defined('SITE_DIR'))
{
	exit;
}

//
// Functions
//
function cron_get_file_lock()
{
	if( file_exists(CRON_ALLOWED) )
	{
#		bb_log(date('H:i:s - ') . getmypid() .' -x-- FILE-LOCK try'. LOG_LF, CRON_LOG_DIR .'cron_check');

		return @rename(CRON_ALLOWED, CRON_RUNNING);
	}
	elseif( file_exists(CRON_RUNNING) )
	{
		cron_release_deadlock();
	}

	return touch(CRON_ALLOWED);
}

function cron_track_running($mode)
{
	@define('CRON_STARTMARK', SITE_DIR . 'triggers/cron_started_at_' . date('Y-m-d_H-i-s') . '_by_pid_' . getmypid());

	if( $mode == 'start' )
	{
		cron_touch_lock_file(CRON_RUNNING);
		file_write('', CRON_STARTMARK);
	}
	elseif( $mode == 'end' )
	{
		@unlink(CRON_STARTMARK);
	}
}

//
// Run cron
//
if( cron_get_file_lock() )
{
	ignore_user_abort(true);
	register_shutdown_function('cron_release_file_lock');
	register_shutdown_function('cron_enable_board');

#	bb_log(date('H:i:s - ') . getmypid() .' --x- FILE-LOCK OBTAINED ###############'. LOG_LF, CRON_LOG_DIR .'cron_check');

	cron_track_running('start');
	require CRON_DIR . 'cron_check.php';
	cron_track_running('end');
}

if( defined('IN_CRON') )
{
	// bb_log(date('H:i:s - ') . getmypid() . ' --x- ALL jobs FINISHED *************************************************' . LOG_LF, CRON_LOG_DIR . 'cron_check');
}
