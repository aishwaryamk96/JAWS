<?php

/*
           8 8888       .8. `8.`888b                 ,8' d888888o.
           8 8888      .888. `8.`888b               ,8'.`8888:' `88.
           8 8888     :88888. `8.`888b             ,8' 8.`8888.   Y8
           8 8888    . `88888. `8.`888b     .b    ,8'  `8.`8888.
           8 8888   .8. `88888. `8.`888b    88b  ,8'    `8.`8888.
           8 8888  .8`8. `88888. `8.`888b .`888b,8'      `8.`8888.
88.        8 8888 .8' `8. `88888. `8.`888b8.`8888'        `8.`8888.
`88.       8 888'.8'   `8. `88888. `8.`888`8.`88'     8b   `8.`8888.
  `88o.    8 88'.888888888. `88888. `8.`8' `8,`'      `8b.  ;8.`8888
    `Y888888 ' .8'       `8. `88888. `8.`   `8'        `Y8888P ,88P'

    JIGSAW ACADEMY WORKFLOW SYSTEM v1
    ---------------------------------
*/

    // Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com/');
		die();
	}

	// This library is used to load and send emails as well as make changes to emails
	// Standard email headers array
	// ["to"], ["from"], ["cc"], ["bcc"], ["subject"], ["body"]

	$GLOBALS["content"] = ["emailer" => ""];

   	// This will put content data for any template into global vars
	function email_content_load($content) {
			$GLOBALS["content"]["emailer"] = $content;
	}

	function send_email($template, $email_info, $content, $onscreen = false) {

		$use_sendgrid = false;
		if ($use_sendgrid) {
			return send_email_sg($template, $email_info, $content, $onscreen);
		}
		else {
			return send_email_php($template, $email_info, $content, $onscreen);
		}

	}

	function send_email_sg($template, $email_info, $content, $onscreen = false) {

		$res = db_query("SELECT * FROM system_email WHERE template_id=".db_sanitize($template).";");
		if (!isset($res[0])) return false;

		// Load content
		email_content_load($content);

		// Prep
		$from = explode(",", isset($email_info["from"]) ? $email_info["from"] : $res[0]["sender"]);
		$to = $GLOBALS["jaws_exec_live"] ? (isset($email_info["to"]) ? $email_info["to"] : "").(isset($res[0]["recipient"]) ? (",".implode(",", explode(";", $res[0]["recipient"]))) : "") : $GLOBALS["jaws_exec_test_email_to"];
		$cc = $GLOBALS["jaws_exec_live"] ? (isset($email_info["cc"]) ? $email_info["cc"] : "").(isset($res[0]["copied"]) ? (",".implode(",", explode(";", $res[0]["copied"]))) : "") : "";
		$bcc = $GLOBALS["jaws_exec_live"] ? (isset($email_info["bcc"]) ? $email_info["bcc"] : "").(isset($res[0]["copied_blind"]) ? (",".implode(",", explode(";", $res[0]["copied_blind"]))) : "") : "";
		$subject = isset($email_info["subject"]) ? $email_info["subject"] : $res[0]["subject"];

		// Render
		$message = render_email($res[0]["template_path"]);
		if (!$message) return false;

		// Onscreen Render?
		if ($onscreen) {
			echo $message;
			return true;
		}

		$to = explode(",", $to);
		$cc = explode(",", $cc);
		$bcc = explode(",", $bcc);

		load_plugin("sendgrid");

		$personalization = new SendGrid\Personalization();

		if (!empty($to)) {

			foreach ($to as $to_email) {

				if (!empty($to_email)) {

					if (array_search($to_email, $cc)) {
						array_splice($cc, array_search($to_email, $cc), 1);
					}
					if (array_search($to_email, $bcc)) {
						array_splice($bcc, array_search($to_email, $bcc), 1);
					}

					$personalization->addTo(new SendGrid\Email(null, $to_email));

				}

			}

		}

		if (!empty($cc)) {

			foreach ($cc as $cc_email) {

				if (!empty($cc_email)) {

					if (array_search($cc_email, $bcc)) {
						array_splice($bcc, array_search($cc_email, $bcc), 1);
					}

					$personalization->addCc(new SendGrid\Email(null, $cc_email));

				}

			}

		}

		if (!empty($bcc)) {
			foreach ($bcc as $bcc_email) {
				if (!empty($bcc_email)) {
					$personalization->addBcc(new SendGrid\Email(null, $bcc_email));
				}
			}
		}

		$content = new SendGrid\Content("text/html", $message);

		if (!empty($from)) {

			if (empty($from[1])) {
				$from[1] = null;
			}
			$from_obj = new SendGrid\Email($from[1], $from[0]);

			$mail = new SendGrid\Mail();
			$mail->setFrom($from_obj);
			$mail->addPersonalization($personalization);
			$mail->setSubject($subject);
			$mail->addContent($content);

			$apiKey = JAWS_AUTH_EMAIL_SENDGRID_KEY;
			$sg = new SendGrid($apiKey);
			$response = $sg->client->mail()->send()->post($mail);

			return ($response->statusCode() == "202" ? true : false);

		}

		return false;

	}

	// This will send an email according to the email headers array
	function send_email_php($template, $email_info, $content, $onscreen = false) {

		$res = db_query("SELECT * FROM system_email WHERE template_id=".db_sanitize($template).";");
		if (!isset($res[0])) return false;

		// Load content
		email_content_load($content);

		// Prep
		$from = explode(",", isset($email_info["from"]) ? $email_info["from"] : $res[0]["sender"]);
		$to = $GLOBALS["jaws_exec_live"] ? (isset($email_info["to"]) ? $email_info["to"] : "").(isset($res[0]["recipient"]) ? (",".implode(",", explode(";", $res[0]["recipient"]))) : "") : $GLOBALS["jaws_exec_test_email_to"];
		$cc = $GLOBALS["jaws_exec_live"] ? (isset($email_info["cc"]) ? $email_info["cc"] : "").(isset($res[0]["copied"]) ? (",".implode(",", explode(";", $res[0]["copied"]))) : "") : "";
		$bcc = $GLOBALS["jaws_exec_live"] ? (isset($email_info["bcc"]) ? $email_info["bcc"] : "").(isset($res[0]["copied_blind"]) ? (",".implode(",", explode(";", $res[0]["copied_blind"]))) : "") : "";
		$subject = !empty($email_info["subject"]) ? $email_info["subject"] : $res[0]["subject"];
		// var_dump($email_info); die;

		// Render
		$html = render_email($res[0]["template_path"]);
		if (!$html) return false;

		// activity_create("ignore", "email.test", "debug", "", "", "", "", $res[0]["template_path"], "pending");

		// Render Plain text email
		$text = render_email_text($res[0]["template_path"]);

		// activity_create("ignore", "email.test", "debug", "", "", "", "", $text, "pending");

		// Onscreen Render?
		if ($onscreen) {
			echo $html;
			return true;
		}

		// Prep more
		$boundary = '----=_NextPart_' . md5(uniqid(time()));

		$header = 'MIME-Version: 1.0' . PHP_EOL;
		$header .= 'Date: ' . date('D, d M Y H:i:s O') . PHP_EOL;
		$header .= 'From: '.(isset($from[1]) ? '=?UTF-8?B?' . base64_encode($from[1]) . '?= <' : '<') . $from[0] . '>' . PHP_EOL;
		$header .= 'Reply-to: '.(isset($from[1]) ? '=?UTF-8?B?' . base64_encode($from[1]) . '?= <' : '<') . $from[0].'>'. PHP_EOL;
		$header .= 'Return-Path: ' . $from[0] . PHP_EOL;
		$header .= 'Cc: ' . $cc . PHP_EOL;
		$header .= 'Bcc: ' . $bcc . PHP_EOL;
		$header .= 'X-Mailer: PHP/' . phpversion() . PHP_EOL;
		$header .= 'Content-Type: multipart/mixed; boundary="' . $boundary . '"' . PHP_EOL . PHP_EOL;

		// create message.
		$message  = '--' . $boundary . PHP_EOL;
		$message .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '_alt"' . PHP_EOL . PHP_EOL;

		if( !empty($text) ){
			$message .= '--' . $boundary . '_alt' . PHP_EOL;
			$message .= 'Content-Type: text/plain; charset="utf-8"' . PHP_EOL;
			$message .= 'Content-Transfer-Encoding: 8bit' . PHP_EOL . PHP_EOL;
			$message .= $text . PHP_EOL;
		}

		$message .= '--' . $boundary . '_alt' . PHP_EOL;
		$message .= 'Content-Type: text/html; charset="utf-8"' . PHP_EOL;
		$message .= 'Content-Transfer-Encoding: 8bit' . PHP_EOL . PHP_EOL;
		$message .= $html . PHP_EOL;
		$message .= '--' . $boundary . '_alt--' . PHP_EOL;


		// Send
		return mail($to, $subject, $message, $header, "-f ".$from[0]);
	}

	// This will load an email php script and execute it and return the output as a string
	function render_email($template_path) {

		ob_start();

		load_template("email", $template_path);
		$email_contents = ob_get_contents();

		ob_end_clean();
		return $email_contents;

	}

	// This will load an email php script and execute it and return the output as a string
	function render_email_text($template_path) {

		$email_contents_text = "";

		ob_start();

		if( file_exists("app/templates/email/".$template_path.".plain.php") ){
			load_template("email", $template_path.".plain");
			$email_contents_text = ob_get_contents();
		}

		ob_end_clean();
		return $email_contents_text;

	}

	// function to send email with attachemnts
	function send_email_with_attachment($template, $email_info, $content, $attachments = array(), $onscreen = false) {

		$res = db_query("SELECT * FROM system_email WHERE template_id=".db_sanitize($template).";");
		if (!isset($res[0])) return false;

		// Load content
		email_content_load($content);

		// Prep
		$from = explode(",", isset($email_info["from"]) ? $email_info["from"] : $res[0]["sender"]);
		$to = $GLOBALS["jaws_exec_live"] ? (isset($email_info["to"]) ? $email_info["to"] : "").(isset($res[0]["recipient"]) ? (",".implode(",", explode(";", $res[0]["recipient"]))) : "") : $GLOBALS["jaws_exec_test_email_to"];
		$cc = $GLOBALS["jaws_exec_live"] ? (isset($email_info["cc"]) ? $email_info["cc"] : "").(isset($res[0]["copied"]) ? (",".implode(",", explode(";", $res[0]["copied"]))) : "") : "";
		$bcc = $GLOBALS["jaws_exec_live"] ? (isset($email_info["bcc"]) ? $email_info["bcc"] : "").(isset($res[0]["copied_blind"]) ? (",".implode(",", explode(";", $res[0]["copied_blind"]))) : "") : "";
		$subject = isset($email_info["subject"]) ? $email_info["subject"] : $res[0]["subject"];

		// Render
		$html = render_email($res[0]["template_path"]);
		if (!$html) return false;

        // Render Plain text email
        $text = render_email_text($res[0]["template_path"]);

        // Onscreen Render?
		if ($onscreen) {
			echo $html . $text;
			return true;
		}

		// set data
		$boundary = '----=_NextPart_' . md5(uniqid(time()));

		// set headers
		$header = 'MIME-Version: 1.0' . PHP_EOL;
		$header .= 'Date: ' . date('D, d M Y H:i:s O') . PHP_EOL;
		$header .= 'From: '.(isset($from[1]) ? '=?UTF-8?B?' . base64_encode($from[1]) . '?= <' : '<') . $from[0] . '>' . PHP_EOL;
		$header .= 'Reply-To: '.(isset($from[1]) ? '=?UTF-8?B?' . base64_encode($from[1]) . '?= <' : '<') . $from[0] . '>' . PHP_EOL;
		$header .= 'Cc: '.$cc . PHP_EOL;
		$header .= 'Bcc: '.$bcc . PHP_EOL;
		$header .= 'Return-Path: ' . $from[0] . PHP_EOL;
		$header .= 'X-Mailer: PHP/' . phpversion() . PHP_EOL;
		$header .= 'Content-Type: multipart/mixed; boundary="' . $boundary . '"' . PHP_EOL . PHP_EOL;


        // create message.
		$message  = '--' . $boundary . PHP_EOL;
		$message .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '_alt"' . PHP_EOL . PHP_EOL;

		if( !empty($text) ){
			$message .= '--' . $boundary . '_alt' . PHP_EOL;
			$message .= 'Content-Type: text/plain; charset="utf-8"' . PHP_EOL;
			$message .= 'Content-Transfer-Encoding: 8bit' . PHP_EOL . PHP_EOL;
			$message .= $text . PHP_EOL;
		}

		$message .= '--' . $boundary . '_alt' . PHP_EOL;
		$message .= 'Content-Type: text/html; charset="utf-8"' . PHP_EOL;
		$message .= 'Content-Transfer-Encoding: 8bit' . PHP_EOL . PHP_EOL;
		$message .= $html . PHP_EOL;
        $message .= '--' . $boundary . '_alt--' . PHP_EOL;

		// add attachments
		if(!empty($attachments)) {
			foreach ($attachments as $attachment) {
				if ( !empty($attachment) && file_exists($attachment) ) {
					$handle = fopen($attachment, 'r');
					$content = fread($handle, filesize($attachment));
					fclose($handle);
					$message .= '--' . $boundary . PHP_EOL;
					$message .= 'Content-Type: application/octet-stream; name="' . basename($attachment) . '"' . PHP_EOL;
					$message .= 'Content-Transfer-Encoding: base64' . PHP_EOL;
					$message .= 'Content-Disposition: attachment; filename="' . basename($attachment) . '"' . PHP_EOL;
					$message .= 'Content-ID: <' . urlencode(basename($attachment)) . '>' . PHP_EOL;
					$message .= 'X-Attachment-Id: ' . urlencode(basename($attachment)) . PHP_EOL . PHP_EOL;
					$message .= chunk_split(base64_encode($content));
				}
			}
		}
		$message .= '--' . $boundary . '--' . PHP_EOL;

		//send the mail
        return mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $message, $header, "-f ".$from[0]);

	}


?>
