<?php

/*
 * Hold variables for sending the mail once it has been tested and stored in the temporary directory.
 *
 * This is retrieving from the ' src/.env ' file all needed datas.
 *
 * DO NOT EDIT THIS FILE ! Instead, edit ' src/.env ' for you own value, and if it was not done already, renames
 * ' src/.env.example ' to ' src/.env ' and edit it as well.
 *
 */

require_once 'variables.php';

// Who's sending the mail - DO NOT EDIT
$SMTP_info = ['server'    => $_ENV['SMTP_SERVER'],
              'mailbox'   => $_ENV['SMTP_USER'],
              'domain'    => $_ENV['SMTP_DOMAIN'],
              'password'  => $_ENV['SMTP_PASSWORD'],
              'port'      => $_ENV['SMTP_PORT']
             ];

// Who's getting the mail - DO NOT EDIT
$RCPT_info = ['mailbox' => $_ENV['RECIPIENT_USER'], 'domain' => $_ENV['RECIPIENT_DOMAIN']];
