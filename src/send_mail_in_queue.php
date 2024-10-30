<?php

/*
 * This script is called by a crontask job, a loop or manually, to send the oldest mail.
 *
 * This, by the way, could be used in other manner, but this is the default behavior.
 *
 */

require_once 'var/variables.php';
require_once 'functions.php';

// '.' and '..' are always here while calling scandir(), if only them are returned, nothing to send (empty directory)
if (!scandir($locations["pending_mails"]) > 2) {

    exit; // Nothing to do

} else { // At least one e-mail has to be send

    $mail_to_send = return_oldest_mail($locations["pending_mails"]); // Attribute the oldest item returned by scandir()
    include 'php_mailer.php'; // Call the script to actually send the mail

}
