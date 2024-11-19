<?php

require __DIR__ . '/../vendor/autoload.php';
require_once '../config/variables.php';
require_once 'functions.php';

/* Set internal character encoding to UTF-8 */
mb_internal_encoding($char_encoding);

// Pre-copy $_POST to $raw_post_copy & avoid to works on the real one, is then updated in the script once it was cleaned
$raw_post_copy = $_POST;

/*
 * HONEY-POT
 *
 * If this is true, then the form was used by a bot or alike, otherwise the non-needed key is removed from $_POST
 * before it is copied to $raw_post_copy.
 *
 * If a bot use this, then exit silently. It's expected to return as a response to this bot-request the same page as a
 * normal and legit user would have, to fool it even more.
 *
 */
if (isset($raw_post_copy[0])) {

    exit;

} else {

    unset($raw_post_copy['first_input']); // Clean the user's data

}

// If sender is asking to get a receipt of the e-mail as a copy (so 2 mails has to be send), remove the key before copy
if (array_key_exists('receive_ack_receipt', $raw_post_copy)) {

    unset($raw_post_copy['receive_ack_receipt']); // Clean the user's data
    $is_receipt_asked = true;

} else {

    $is_receipt_asked = false; // Copy wasn't asked

}

// Update the copy of $_POST, since now it's clean for the workflow
$post_copy = $raw_post_copy;

// Test if $_POST isn't empty
$user_input_count = count($post_copy);

if (empty($post_copy)) {

    exit;

}

// Get the number of expected <input/> and <textarea></textarea> HTML's tag that the user was supposed to fill
$expected_input_count = count($mail_form);

// Test if $_POST get the number of expected keys
if (($user_input_count != $expected_input_count)) {

    exit;

}

// Check if the domain in the user's input isn't in one of the non-trusty ESP domain
$is_domain_untrusty = reject_disposable_email_domain($post_copy['email']);

if ($is_domain_untrusty) {

    // Test if the locations to store request was made with disposable mail's domain
    if (!file_exists($locations["logs_mail_disposable"])) {

        exit;

    }

    // Copy the file to 'mail_dir/UNTRUSTY/DISPOSABLE/' without passing it to store_sended_mail_to_logs()
    store_to_json($post_copy, $locations["logs_mail_disposable"], $is_receipt_asked);

    exit; // User input is listed as untrusty, exiting.

}

// Execute all the tests (lenght and pattern matching) on the clean version of user's input.
$send_mail_test = validate_email_sending($post_copy, $field_len_list_min, $field_len_list_max, $field_type);
if (!$send_mail_test) {

    exit;

}

// Test if the locations to store pending mails exist and is accessible. If true, try to store the mail in the temp dir
if (!file_exists($locations["pending_mails"])) {

    exit;

}

// Store the mail file and redirect the browser on another page stating it was accepted and will be sended soon
store_to_json($post_copy, $locations["pending_mails"], $is_receipt_asked, $target_for_redirection);
