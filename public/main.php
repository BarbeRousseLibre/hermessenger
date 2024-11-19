<?php

/*
 * Main entry point for Hermessenger.
 *
 * This file has to be in the document root.
 *
 * The web mail form should target this file to redirect the user's input data to Hermessenger:
 * ' <form action="/main.php" method="post"> '
 *
 * This file should be accessible to your web server user.
 *
 */

// Load composer
require __DIR__ . '/../vendor/autoload.php';

// Redirect user's input for testing it
require_once __DIR__ . '/../vendor/barberousselibre/hermessenger/src/checking_form.php';
