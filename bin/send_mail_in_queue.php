<?php

/*
 * This script is called by a crontask job, a loop or manually (CGI), to send the oldest mail.
 *
 * This, by the way, could be used in other manner, but this is the expected behavior (crontask job).
 *
 */

require_once(__DIR__) . '/../config/settings.php';
require_once(__DIR__) . '/../config/form_config.php';
require_once(__DIR__) . '/../config/server_path_config.php';
require_once(__DIR__ . '/../src/functions.php');

$file_list = scandir($locations["pending_mails"]); // Retrieve all the files, increasing order (oldest to newest)

/*
 * Select only the first file (as scandir is used, the oldest) starting with the prefix for pending mail and
 * avoiding UNIX path ('.', '..') values as other non-mail file (such as '.gitkeep' or alike).
 *
 */
foreach ($file_list as $file) {

    if (str_starts_with($file, "mail_pending_")) {

        echo "Mail to send:\n=> \" $file \"\n";

        $path_to_mail = $locations["pending_mails"] . $file; // Gives ' php_mailer.php ' the path to the mail to send

        include_once(__DIR__ . '/../src/php_mailer.php'); // Send the mail

        exit; // This could be avoided ?

    }

}

// Reaching this point, there was no mail to send
exit;
