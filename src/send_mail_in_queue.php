<?php

/*
 * This script is called by a crontask job, a loop or manually, to send the oldest mail.
 *
 * This, by the way, could be used in other manner, but this is the default behavior.
 *
 */

require_once 'var/variables.php';
require_once 'functions.php';

// If false, no mail to send. Otherwise call 'php_mailer.php' once $mail_to_send contains an actual file.
if (!$mail_to_send = return_oldest_mail($locations["pending_mails"])) {

    exit;

} else {

    include 'php_mailer.php'; // Call the script to actually send the mail

}

