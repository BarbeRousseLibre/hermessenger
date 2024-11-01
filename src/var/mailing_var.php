<?php

/*
 * Hold variables for sending the mail once it has been tested and stored in the temporary directory.
 */

require_once 'variables.php';

// Who's sending the mail
$SMTP_info = [
    'server'    => $_ENV['SMTP_SERVER'],
    'mailbox'   => $_ENV['SMTP_USER'],
    'domain'    => $_ENV['SMTP_DOMAIN'],
    'password'  => $_ENV['SMTP_PASSWORD'],
    'port'      => $_ENV['SMTP_PORT']
];

// Who's getting the mail
$RCPT_info = [
    'mailbox'   => $_ENV['RECIPIENT_USER'],
    'domain'    => $_ENV['RECIPIENT_DOMAIN']
];
