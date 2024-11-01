<?php

/* DO NOT EDIT THIS FILE ! INSTEAD USE 'var/variables.php' WHEN NECESSARY TO EDIT VALUES ! */

/* Set internal character encoding to UTF-8 */
mb_internal_encoding("UTF-8");

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$phpdotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$phpdotenv->load();

require 'var/variables.php';
require 'var/mailing_var.php';

// Building sender and recipient e-mail address informations from PHPDotenv's method (.env file)
$sender = $SMTP_info['mailbox'] . $SMTP_info['domain'];
$recipient = $RCPT_info['mailbox'] . $RCPT_info['domain'];

// Building mail path to the old destination (in the pending's mail directory) as new one (to ACCEPTED/REJECTED dir)
$old_mail_path = $mail_to_send["pending_mail_location"] . $mail_to_send["mail_file_name"];
$new_mail_path = $locations["logs_mail"] . $mail_to_send['mail_file_name'];

// Who's gonna get the e-mail, firstname and secondname has to be separated by a white space for fashion
$cgi_username = $mail_to_send['firstname'] . " " . $mail_to_send['secondname'];

// Define who's sending the mail
$mail = new PHPMailer(true);
$mail->From = $sender;
$mail->FromName = $cgi_username;
$mail->CharSet = "UTF-8"; // Force UTF-8 while sending mail, adding 'mb_internal_encoding("UTF-8")' is not enough.

try {

    /* ESP's server settings */
    $mail->SMTPDebug  = SMTP::DEBUG_SERVER;
    //$mail->SMTPDebug  = false;
    $mail->isSMTP(true);
    $mail->Host       = $SMTP_info["server"];
    $mail->SMTPAuth   = true;
    $mail->Username   = $sender;
    $mail->Password   = $SMTP_info["password"];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = $SMTP_info["port"];

    /* Recipients informations */
    $mail->addAddress($recipient); // Main recipient

    // If a receipt was asked
    $current_mail = file($old_mail_path);
    $addRCPT = str_contains($current_mail[7], "send_copy|true");
    if ($addRCPT) {

        $mail->addCC($mail_to_send['email']);

    }

    /* Content */
    $mail->isHTML(false);
    $mail->Subject      = $mail_to_send['subject'];
    $mail->Body         = $mail_to_send['body'];
    $mail->AltBody      = $mail_to_send['body'];

    /* Sending */
    $mail->send();

    // Test if the new logs path for the current mail file exist
    if (!file_exists($locations["logs_mail_accepted"])) {

        exit;

    }

    date_default_timezone_set('Europe/Paris'); // This is ugly, to fix
    store_sended_mail_to_logs($old_mail_path, true, $locations);

    echo "Message has been sent the " . date('Y-m-d, \a\t H:i:s') . ".\n";

} catch (Exception $e) {

    date_default_timezone_set('Europe/Paris'); // This is ugly, to fix

    // Test if the new logs path for the current mail file exist and has good perms
    if (!file_exists($locations["logs_mail_rejected"])) {

        exit;

    }

    store_sended_mail_to_logs($old_mail_path, false, $locations);

    echo "Message could not been sent. Mailer error: {$mail->ErrorInfo}";

}
