<?php

/*
 * This is a copy/pasted source code from the PHPMailer project, modified to works as expected with Hermessenger.
 *
 * Original file: https://github.com/PHPMailer/PHPMailer?tab=readme-ov-file#a-simple-example
 *
 */

// Set internal character encoding
mb_internal_encoding($char_encoding);

// Define timezone
date_default_timezone_set($timezone);

require dirname(__DIR__, 4) . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$phpdotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$phpdotenv->load();

require 'var/mailing_var.php';

// Retrieve the mail's information from the decoded json file
$mail_json_file = file_get_contents($path_to_mail);
$mail_decoded = json_decode($mail_json_file);

// This is useless but working as it is for now. To change later.
$mail_clean = ["firstname" => $mail_decoded->firstname,
               "secondname" => $mail_decoded->secondname,
               "email" => $mail_decoded->email,
               "zipcode" => $mail_decoded->zipcode,
               "subject_prefix_list" => $mail_decoded->subject_prefix_list,
               "body" => $mail_decoded->body, // Forcing carriage and line return
               "IP" => $mail_decoded->IP,
               "date_and_time" => $mail_decoded->date_and_time,
               "send_copy" => $mail_decoded->send_copy
              ];

// Create a PHPMailer's instance, true enable exception
$mail = new PHPMailer(true);

// Retrieve sensitive info from ' src/.env ' file.
$internal_sender = $SMTP_info['mailbox'] . $SMTP_info['domain'];
$recipient = $RCPT_info['mailbox'] . $RCPT_info['domain'];

// Define who's sending the mail
$mail->From = $internal_sender;
$mail->FromName = $mail_clean["email"];

// Define charset encoding for sending mail
$mail->CharSet = $char_encoding;

try {

    /* ESP's server settings */
    //$mail->SMTPDebug  = SMTP::DEBUG_SERVER;
    $mail->SMTPDebug  = true; // This is not a good way to define this here (?)
    $mail->isSMTP(true);
    $mail->Host       = $SMTP_info["server"];
    $mail->SMTPAuth   = true;
    $mail->Username   = $internal_sender;
    $mail->Password   = $SMTP_info["password"];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = $SMTP_info["port"];

    // Recipients informations - Who's gonna get the mail from the form
    $mail->addAddress($recipient);

    // If a receipt was asked, define addCC to the sender's mailbox
    if ($mail_clean["send_copy"]) {

        $mail->addCC($mail_clean["email"]);

    }

    // Content
    $mail->isHTML(false);

    // WIP.
    $unicode_Envelope = "✉";
    $mail->Subject     = '=?UTF-8?B?' . base64_encode($unicode_Envelope) . '?=' . " [" . $mail_clean["subject_prefix_list"] . "] " ;

    // Otherwise we don't know who sended it
    $mail->Body         = "Was sended from ".
                          $mail_clean["email"] .
                          " [ " . $mail_clean["firstname"] .
                          " " .
                          $mail_clean["secondname"] . " ]" .
                          " [ " . $mail_clean["zipcode"] . " ]\n\n" .
                          $mail_clean["body"];
    //$mail->AltBody      = $mail_clean["body"]; // Using this turns '\r\n' into nothing in the mail once sended

    /* Sending */
    $mail->send();

    // Test if the new logs path for the current mail file exist
    if (!file_exists($locations["logs_mail_accepted"])) {

        echo "ACCEPTED MAIL ERROR ABOUT LOGGING:\n";
        echo "There is no " . $locations["logs_mail_accepted"] . " directory to moves the mail.\n";
        echo "The mail sending was done nonetheless, at " . date('Y-m-d, \a\t H:i:s') . ".\n";
        exit;

    }

    store_sended_mail_to_logs($path_to_mail, true, $locations["logs_mail_accepted"]);

    echo "Message has been sent the " . date('Y-m-d, \a\t H:i:s') . " and file was copied to " .
         $locations["logs_mail_accepted"] . "\n";

} catch (Exception $e) {

    // Test if the new logs path for the current mail file exist and has good perms
    if (!file_exists($locations["logs_mail_rejected"])) {

        echo "REJECTED MAIL ERROR ABOUT LOGGING:\n";
        echo "There is no " . $locations["logs_mail_rejected"] . " directory to moves the mail.\n";
        echo "The mail sending was done nonetheless, at " .  date('Y-m-d, \a\t H:i:s') . ".\n";
        exit;

    }

    store_sended_mail_to_logs($path_to_mail, false, $locations["logs_mail_rejected"]);

    echo "Message could not been sent, at " . date('Y-m-d, \a\t H:i:s') . ".\nMailer error: {$mail->ErrorInfo}";

}
