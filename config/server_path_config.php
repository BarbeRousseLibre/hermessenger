<?php

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
#                                                                                 #
#  This should below probably never be edited unless you know what you are doing. #
#                                                                                 #
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

/*
 * This file is not for edition. You could actually modify it, but it would ask you to modify a few things to keep
 * Hermessenger to works.
 *
 * This file contains paths needed for Hermessenger.
 *
 * If you are looking to configure Hermessenger, please see ' docs/SETTINGS.md ' and ' docs/FORM_CONFIG.md '.
 *
 * Editing this file would mostly lead to a broken application.
 *
 * Do it only if you know what you are doing.
 *
 */

// The directory where is installed Hermessenger's source (from packagist probably)
$hermessenger_source = $document_root_parent . '/vendor/barberousselibre/hermessenger';

// Main directory for holding all the e-mails, pending, accepted, rejected, untrusty, etc.
$mail_dir = "mail_dir";

// Location for mails before being send, if they are valid.
$pending_mail_directory = "pending_mail_directory";

// The new location the mail is moved to once sended (and accepted) by PHPMailer
$accepted_mail_dir = "ACCEPTED";

// The new location the mail is moved to once sended (but rejected) by PHPMailer
$rejected_mail_dir = "REJECTED";

// The new location for mails using a non trusty domain (disposable), not sended
$disposable_mail_dir = "UNTRUSTY/DISPOSABLE";

// Various locations (server directories and path) builded with above variables
$locations =
[
    "pending_mails"        => $hermessenger_source . "/var/" . $pending_mail_directory . "/",
    "logs_mail"            => $hermessenger_source . "/var/" . $mail_dir . "/",
    "logs_mail_accepted"   => $hermessenger_source . "/var/" . $mail_dir . "/" . $accepted_mail_dir . "/",
    "logs_mail_rejected"   => $hermessenger_source . "/var/" . $mail_dir . "/" . $rejected_mail_dir . "/",
    "logs_mail_disposable" => $hermessenger_source . "/var/" . $mail_dir . "/" . $disposable_mail_dir . "/"
];

mb_internal_encoding($char_encoding);

// Build the URL for redirecting the client once the mail has been accepted by Hermessenger, before any sending
$accepted_sending_redirection = $redir_location["scheme"] .
                                "://" .
                                $redir_location["domain_name"] .
                                "/" .
                                $redir_location["ressource_path_accepted"];

// Build the URL for redirecting the client once the mail has been rejected by Hermessenger, with no futur sending
$rejected_sending_redirection = $redir_location["scheme"] .
                                "://" .
                                $redir_location["domain_name"] .
                                "/" .
                                $redir_location["ressource_path_rejected"];
