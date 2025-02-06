<?php

/*
 * Main entry point for Hermessenger.
 *
 * This file has to be in the document root.
 *
 * The web mail form should target this file to redirect the user's input data to Hermessenger:
 * ' <form action="/main.php" method="post"> '
 *
 * This file should be accessible to your web server user and only this file (from Hermessenger).
 *
 */

require __DIR__ . '/../vendor/barberousselibre/hermessenger/config/server_path_config.php';

// Load composer
require __DIR__ . '/../vendor/autoload.php';

/*
 * Pre-copy $_POST to $raw_post_copy & avoid to works on the real datas, then is updated in the script once it was
 * cleaned for further testing.
 *
 */
$raw_post_copy = $_POST;

// Redirect user's input for testing it
require_once __DIR__ . $hermessenger_source . 'src/checking_form.php';
