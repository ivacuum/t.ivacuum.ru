<?php

//
// The emailer class has support for attaching files, that isn't implemented
// in the 2.0 release but we can probable find some way of using it in a future
// release
//
class emailer
{
	var $msg, $subject, $extra_headers;
	var $addresses, $reply_to, $from;
	var $use_smtp;

	var $tpl_msg = array();

	function __construct($use_smtp)
	{
		$this->reset();
		$this->use_smtp = $use_smtp;
		$this->reply_to = $this->from = '';
	}

	// Resets all the data (address, template file, etc etc to default
	function reset()
	{
		$this->addresses = array();
		$this->vars = $this->msg = $this->extra_headers = '';
	}

	// Sets an email address to send to
	function email_address($address)
	{
		$this->addresses['to'] = trim($address);
	}

	function cc($address)
	{
		$this->addresses['cc'][] = trim($address);
	}

	function bcc($address)
	{
		$this->addresses['bcc'][] = trim($address);
	}

	function replyto($address)
	{
		$this->reply_to = trim($address);
	}

	function from($address)
	{
		$this->from = trim($address);
	}

	// set up subject for mail
	function set_subject($subject = '')
	{
		$this->subject = trim(preg_replace('#[\n\r]+#s', '', $subject));
	}

	// set up extra mail headers
	function extra_headers($headers)
	{
		$this->extra_headers .= trim($headers) . "\n";
	}

	function use_template($template_file, $template_lang = '')
	{
		global $bb_cfg;

		if (trim($template_file) == '')
		{
			message_die(GENERAL_ERROR, 'No template file set', '', __LINE__, __FILE__);
		}

		if (trim($template_lang) == '')
		{
			$template_lang = $bb_cfg['default_lang'];
		}

		if (empty($this->tpl_msg[$template_lang . $template_file]))
		{
			$tpl_file = SITE_DIR . 'language/lang_' . $template_lang . '/email/' . $template_file . '.tpl';

			if (!@file_exists(@phpbb_realpath($tpl_file)))
			{
				$tpl_file = SITE_DIR . 'language/lang_' . $bb_cfg['default_lang'] . '/email/' . $template_file . '.tpl';

				if (!@file_exists(@phpbb_realpath($tpl_file)))
				{
					message_die(GENERAL_ERROR, 'Could not find email template file :: ' . $template_file, '', __LINE__, __FILE__);
				}
			}

			if (!($fd = @fopen($tpl_file, 'r')))
			{
				message_die(GENERAL_ERROR, 'Failed opening template file :: ' . $tpl_file, '', __LINE__, __FILE__);
			}

			$this->tpl_msg[$template_lang . $template_file] = fread($fd, filesize($tpl_file));
			fclose($fd);
		}

		$this->msg = $this->tpl_msg[$template_lang . $template_file];

		return true;
	}

	// assign variables
	function assign_vars($vars)
	{
		$this->vars = (empty($this->vars)) ? $vars : $this->vars . $vars;
	}

	// Send the mail out to the recipients set previously in var $this->address
	function send()
	{
		global $bb_cfg, $lang, $db;

		if ($bb_cfg['emailer_disabled'])
		{
			return;
		}

    	// Escape all quotes, else the eval will fail.
		$this->msg = str_replace ("'", "\'", $this->msg);
		$this->msg = preg_replace('#\{([a-z0-9\-_]*?)\}#is', "' . $\\1 . '", $this->msg);

		// Set vars
		reset ($this->vars);
		while (list($key, $val) = each($this->vars))
		{
			$$key = $val;
		}

		eval("\$this->msg = '$this->msg';");

		// Clear vars
		reset ($this->vars);
		while (list($key, $val) = each($this->vars))
		{
			unset($$key);
		}

		// We now try and pull a subject from the email body ... if it exists,
		// do this here because the subject may contain a variable
		$drop_header = '';
		$match = array();
		if (preg_match('#^(Subject:(.*?))$#m', $this->msg, $match))
		{
			$this->subject = (trim($match[2]) != '') ? trim($match[2]) : (($this->subject != '') ? $this->subject : 'No Subject');
			$drop_header .= '[\r\n]*?' . preg_quote($match[1], '#');
		}
		else
		{
			$this->subject = (($this->subject != '') ? $this->subject : 'No Subject');
		}

		if (preg_match('#^(Charset:(.*?))$#m', $this->msg, $match))
		{
			$this->encoding = (trim($match[2]) != '') ? trim($match[2]) : trim($lang['CONTENT_ENCODING']);
			$drop_header .= '[\r\n]*?' . preg_quote($match[1], '#');
		}
		else
		{
			$this->encoding = !empty($lang['CONTENT_ENCODING']) ? trim($lang['CONTENT_ENCODING']) : $bb_cfg['email_default_charset'];
		}

		if ($drop_header != '')
		{
			$this->msg = trim(preg_replace('#' . $drop_header . '#s', '', $this->msg));
		}

		$to = @$this->addresses['to'];

		$cc = (@count($this->addresses['cc'])) ? implode(', ', $this->addresses['cc']) : '';
		$bcc = (@count($this->addresses['bcc'])) ? implode(', ', $this->addresses['bcc']) : '';

		// Build header
		// $this->extra_headers = (($this->reply_to != '') ? "Reply-to: $this->reply_to\n" : '') . (($this->from != '') ? "From: $this->from\n" : "From: " . $bb_cfg['board_email'] . "\n") . "Return-Path: " . $bb_cfg['board_email'] . "\nMessage-ID: <" . md5(uniqid(time())) . "@" . $bb_cfg['server_name'] . ">\nMIME-Version: 1.0\nContent-type: text/plain; charset=" . $this->encoding . "\nContent-transfer-encoding: 8bit\nDate: " . date('r', time()) . "\nX-Priority: 3\nX-MSMail-Priority: Normal\nX-Mailer: PHP\nX-MimeOLE: Produced By phpBB2\n" . $this->extra_headers . (($cc != '') ? "Cc: $cc\n" : '')  . (($bcc != '') ? "Bcc: $bcc\n" : '');
		$this->extra_headers = (($this->reply_to != '') ? "Reply-to: $this->reply_to\n" : '') . (($this->from != '') ? "From: $this->from\n" : "From: " . $bb_cfg['board_email'] . "\n") . "Return-Path: " . $bb_cfg['board_email'] . "\nMessage-ID: <" . md5(uniqid(time())) . "@" . $bb_cfg['server_name'] . ">\nMIME-Version: 1.0\nContent-type: text/plain; charset=" . $this->encoding . "\nContent-transfer-encoding: 8bit\nDate: " . date('r', time()) . "\nX-Priority: 3\nX-MSMail-Priority: Normal\nX-Mailer: Microsoft Office Outlook, Build 11.0.5510\nX-MimeOLE: Produced By Microsoft MimeOLE V6.00.2800.1441\nX-Sender: " . $bb_cfg['board_email'] . "\n" . $this->extra_headers . (($cc != '') ? "Cc: $cc\n" : '')  . (($bcc != '') ? "Bcc: $bcc\n" : '');

		// Send message ... removed $this->encode() from subject for time being
		if ( $this->use_smtp )
		{
			if ( !defined('SMTP_INCLUDED') )
			{
				require SITE_DIR . 'includes/smtp.php';
			}

			$result = smtpmail($to, $this->subject, $this->msg, $this->extra_headers);
		}
		else
		{
			$empty_to_header = ($to == '') ? TRUE : FALSE;
			$to = ($to == '') ? (($bb_cfg['sendmail_fix']) ? ' ' : 'Undisclosed-recipients:;') : $to;

			$result = @mail($to, mail_encode($this->subject), preg_replace("#(?<!\r)\n#s", "\n", $this->msg), $this->extra_headers);

			if (!$result && !$bb_cfg['sendmail_fix'] && $empty_to_header)
			{
				$to = ' ';

				bb_update_config(array('sendmail_fix' => 1));

				$bb_cfg['sendmail_fix'] = 1;
				$result = @mail($to, $this->subject, preg_replace("#(?<!\r)\n#s", "\n", $this->msg), $this->extra_headers);
			}
		}

		// Did it work?
		if (!$result)
		{
//		message_die(GENERAL_ERROR, 'Failed sending email :: ' . (($this->use_smtp) ? 'SMTP' : 'PHP') . ' :: ' . $result, '', __LINE__, __FILE__);
		}

		return true;
	}

	// Encodes the given string for proper display for this encoding ... nabbed
	// from php.net and modified. There is an alternative encoding method which
	// may produce lesd output but it's questionable as to its worth in this
	// scenario IMO
	function encode($str)
	{
		if ($this->encoding == '')
		{
			return $str;
		}

		// define start delimimter, end delimiter and spacer
		$end = "?=";
		$start = "=?$this->encoding?B?";
		$spacer = "$end\r\n $start";

		// determine length of encoded text within chunks and ensure length is even
		$length = 75 - strlen($start) - strlen($end);
		$length = floor($length / 2) * 2;

		// encode the string and split it into chunks with spacers after each chunk
		$str = chunk_split(base64_encode($str), $length, $spacer);

		// remove trailing spacer and add start and end delimiters
		$str = preg_replace('#' . preg_quote($spacer, '#') . '$#', '', $str);

		return $start . $str . $end;
	}

	//
	// Attach files via MIME.
	//
	function attachFile($filename, $mimetype = "application/octet-stream", $szFromAddress, $szFilenameToDisplay)
	{
		global $lang;
		$mime_boundary = "--==================_846811060==_";

		$this->msg = '--' . $mime_boundary . "\nContent-Type: text/plain;\n\tcharset=\"" . $lang['CONTENT_ENCODING'] . "\"\n\n" . $this->msg;

		if ($mime_filename)
		{
			$filename = $mime_filename;
			$encoded = $this->encode_file($filename);
		}

		$fd = fopen($filename, "r");
		$contents = fread($fd, filesize($filename));

		$this->mimeOut = "--" . $mime_boundary . "\n";
		$this->mimeOut .= "Content-Type: " . $mimetype . ";\n\tname=\"$szFilenameToDisplay\"\n";
		$this->mimeOut .= "Content-Transfer-Encoding: quoted-printable\n";
		$this->mimeOut .= "Content-Disposition: attachment;\n\tfilename=\"$szFilenameToDisplay\"\n\n";

		if ( $mimetype == "message/rfc822" )
		{
			$this->mimeOut .= "From: ".$szFromAddress."\n";
			$this->mimeOut .= "To: ".$this->emailAddress."\n";
			$this->mimeOut .= "Date: ".date("D, d M Y H:i:s") . " UT\n";
			$this->mimeOut .= "Reply-To:".$szFromAddress."\n";
			$this->mimeOut .= "Subject: ".$this->mailSubject."\n";
			// $this->mimeOut .= "X-Mailer: PHP/".phpversion()."\n";
			$this->mimeOut .= "X-Priority: 0\n";
			$this->mimeOut .= "X-Mailer: Microsoft Office Outlook, Build 11.0.5510\n";
			$this->mimeOut .= "X-MimeOLE: Produced By Microsoft MimeOLE V6.00.2800.1441\n";
			$this->mimeOut .= "X-Sender: " . $bb_cfg['board_email'] . " \n";
			$this->mimeOut .= "MIME-Version: 1.0\n";
		}

		$this->mimeOut .= $contents."\n";
		$this->mimeOut .= "--" . $mime_boundary . "--" . "\n";

		return $out;
		// added -- to notify email client attachment is done
	}

	function getMimeHeaders($filename, $mime_filename="")
	{
		$mime_boundary = "--==================_846811060==_";

		if ($mime_filename)
		{
			$filename = $mime_filename;
		}

		$out = "MIME-Version: 1.0\n";
		$out .= "Content-Type: multipart/mixed;\n\tboundary=\"$mime_boundary\"\n\n";
		$out .= "This message is in MIME format. Since your mail reader does not understand\n";
		$out .= "this format, some or all of this message may not be legible.";

		return $out;
	}

	//
   // Split string by RFC 2045 semantics (76 chars per line, end with \r\n).
	//
	function myChunkSplit($str)
	{
		$stmp = $str;
		$len = strlen($stmp);
		$out = "";

		while ($len > 0)
		{
			if ($len >= 76)
			{
				$out .= substr($stmp, 0, 76) . "\r\n";
				$stmp = substr($stmp, 76);
				$len = $len - 76;
			}
			else
			{
				$out .= $stmp . "\r\n";
				$stmp = "";
				$len = 0;
			}
		}
		return $out;
	}

	//
   // Split the specified file up into a string and return it
	//
	function encode_file($sourcefile)
	{
		if (is_readable(phpbb_realpath($sourcefile)))
		{
			$fd = fopen($sourcefile, "r");
			$contents = fread($fd, filesize($sourcefile));
	      $encoded = $this->myChunkSplit(base64_encode($contents));
	      fclose($fd);
		}

		return $encoded;
	}

} // class emailer

/**
* Encodes the given string for proper display in UTF-8.
*
* This version is using base64 encoded data. The downside of this
* is if the mail client does not understand this encoding the user
* is basically doomed with an unreadable subject.
*
* Please note that this version fully supports RFC 2045 section 6.8.
*
* @param string $eol End of line we are using (optional to be backwards compatible)
*/
function mail_encode($str, $eol = "\r\n")
{
	// define start delimimter, end delimiter and spacer
	$start = "=?UTF-8?B?";
	$end = "?=";
	$delimiter = "$eol ";

	// Maximum length is 75. $split_length *must* be a multiple of 4, but <= 75 - strlen($start . $delimiter . $end)!!!
	$split_length = 60;
	$encoded_str = base64_encode($str);

	// If encoded string meets the limits, we just return with the correct data.
	if( strlen($encoded_str) <= $split_length )
	{
		return $start . $encoded_str . $end;
	}

	// If there is only ASCII data, we just return what we want, correctly splitting the lines.
	if( strlen($str) === mb_strlen($str) )
	{
		return $start . implode($end . $delimiter . $start, str_split($encoded_str, $split_length)) . $end;
	}

	// UTF-8 data, compose encoded lines
	$array = utf8_str_split($str);
	$str = '';

	while( sizeof($array) )
	{
		$text = '';

		while( sizeof($array) && intval((strlen($text . $array[0]) + 2) / 3) << 2 <= $split_length )
		{
			$text .= array_shift($array);
		}

		$str .= $start . base64_encode($text) . $end . $delimiter;
	}

	return substr($str, 0, -strlen($delimiter));
}

/**
* UTF-8 aware alternative to str_split
* Convert a string to an array
*
* @author Harry Fuecks
* @param string $str UTF-8 encoded
* @param int $split_len number to characters to split string by
* @return array characters in string reverses
*/
function utf8_str_split($str, $split_len = 1)
{
	if( !is_int($split_len) || $split_len < 1 )
	{
		return false;
	}

	$len = mb_strlen($str);
	if( $len <= $split_len )
	{
		return array($str);
	}

	preg_match_all('/.{' . $split_len . '}|[^\x00]{1,' . $split_len . '}$/us', $str, $ar);
	return $ar[0];
}
