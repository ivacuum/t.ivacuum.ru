<?php

if (!defined('SITE_DIR'))
{
	exit;
}

// bb_log(date('H:i:s - ') . getmypid() .' --x- SELECT jobs'. LOG_LF, CRON_LOG_DIR .'cron_check');

// Get cron jobs
$cron_jobs = $db->fetch_rowset("
	SELECT * FROM bb_cron
	WHERE cron_active = 1
		AND next_run <= NOW()
	ORDER BY run_order
");

// Run cron jobs
if( $cron_jobs )
{
	// bb_log(date('H:i:s - ') . getmypid() . ' --x- RUN jobs' . LOG_LF, CRON_LOG_DIR . 'cron_check');

	foreach( $cron_jobs as $job )
	{
		if( $job['disable_board'] )
		{
			cron_disable_board();
			sleep(10);
			break;
		}
	}

	require CRON_DIR . 'cron_run.php';

	// Update cron_last_check
	bb_update_config(array('cron_last_check' => TIMENOW));
}
else
{
	// bb_log(date('H:i:s - ') . getmypid() .' --x- no active jobs found ----------------------------------------------'. LOG_LF, CRON_LOG_DIR .'cron_check');
}
