<?php

/* Set internal character encoding to UTF-8 */
mb_internal_encoding("UTF-8");

require __DIR__ . '/../vendor/autoload.php';
require_once 'var/variables.php';
require_once 'functions.php';

// Work on a copy of the $_POST
$post_copy = $_POST;

// Test if $_POST contain the honey-pot entry, if that's so, reject the request and logs it.
/* WIP */

// Test if $_POST isn't empty
$user_input_count = count($post_copy);
if (empty($post_copy)) {

    exit;

// Test if $_POST get the number of expected key=>value, 6 with 'receive_ack_receipt'.
} else if (($user_input_count > 6 AND $user_input_count < 5)) {

    exit;

}

// WIP //
// Check if the domain in the user's input isn't in one of the non-trusty ESP domain

// If sender is asking to get a receipt of the e-mail as a copy (so 2 mails has to be send), remove the key
if (array_key_exists('receive_ack_receipt', $post_copy)) {

    unset($post_copy['receive_ack_receipt']);
    $is_receipt_asked = true;

}

// Execute all the tests (lenght and pattern matching)
$send_mail_test = validate_email_sending($post_copy, $field_len_list);
if (!$send_mail_test) {

    exit;

}

// Test if the locations to store pending mails exist and is accessible. If true, try to store the mail in the temp dir
if (!file_exists($locations["pending_mails"])) {

    exit;

} else if (!$store_mail = store_to_plaintext($post_copy, $locations["pending_mails"], $is_receipt_asked)) {

    return false;

} else { // Everything is good

    return true;

}
