<?php

/*
 * This file is used to link to the proper path, timezone as HTML input's field/textarea. As other directories needed
 * for suspicious request.
 *
 * Per default, you get variables.example.php, you have to manually renames it or copy it to
 * variables.php to enable it.
 *
 */

/* - * - * - * - * - * CHANGE THIS ACCORDING YOUR OWN NEED - * - * - * - * - * - * - * - * - * - */

// Timezone to use, per default Europe/Paris, change according your location / need.
$timezone = "Europe/Paris";

/*
 * Change the $document_root variable accordingly to your own configuration.
 * All these paths has to be ended with a slash ('/').
 */
$document_root = "/path/to/your/docroot/";
$locations = ["pending_mails"           => $document_root . "temp_mail_directory/",
              "logs_mail"               => $document_root . "mail_dir/",
              "logs_mail_accepted"      => $document_root . "mail_dir/ACCEPTED/",
              "logs_mail_rejected"      => $document_root . "mail_dir/REJECTED/",
              "quarantine"              => $document_root . "quarantine/"
             ];

/* Define the quarantine locations for suspicious mail request sending, too fast request, etc.
 *
 * "suspicious" is used for mails that was allowed, but a copy is written here (for, as example, later analysis), as
 * it is logged into the logs file for these.
 *
 * "too_fast" is used for mails that was sended too fast, see 'is_request_too_fast()' into src/functions.php for more
 * details.
 *
 * "rejected" is for mails that was not sended, but if a copy was asked from the administrator while using
 * 'is_request_too_fast()' function, see src/functions.php for more details.
 *
 * "logs" contains a plaintext file were all "suspicious", "too_fast" & "rejected" are logged into a log file for each
 * request.
 */
$quarantine_locations = ["suspicious"   => $locations["quarantine"] . "suspicious/",
                         "too_fast"     => $locations["quarantine"] . "fast_request/",
                         "rejected"     => $locations["quarantine"] . "rejected/",
                         "logs"         => $locations["quarantine"] . "logs/"
                        ];
$quarantine_log_file = $quarantine_locations["logs"] . "todefinebaby"; // WIP, see this a bit later.

/* - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - */

/* These below are safe to change, but that might be not necessary */
// ^ WIP, to improve !

/* Set internal character encoding to UTF-8 */
mb_internal_encoding("UTF-8");

// Per default, define there is no copy to send
$is_receipt_asked = false;

// Define the range of each field between a *_min & *_max key's value
$field_len_list = ['firstname_min' => 2, 'firstname_max' => 32,
                   'secondname_min' => 2, 'secondname_max' => 32,
                   'email_min' => 8, 'email_max' => 32,
                   'subject_min' => 8, 'subject_max' => 48,
                   'body_min' => 64, 'body_max' => 2048
                  ];

// Define the range for adding a prefix for subject & body field once the mail is sended
$prefix_len = ['prefix_subject_min' => 1, 'prefix_subject_max' => 16,
               'prefix_body_min' => 1, 'prefix_body_max' => 512,
              ];

$mail_form = [
    0 => ['id' => 'firstname',
          'name' => 'firstname',
          'readable' => 'First name',
          'type' => 'text',
          'htmltag' => 'input',
          'placeholder' => 'Jean',
         ],

    1 => ['id' => 'secondname',
          'name' => 'secondname',
          'readable' => 'Second name',
          'type' => 'text',
          'htmltag' => 'input',
          'placeholder' => 'Dupont',
         ],

    2 => ['id' => 'email',
          'name' => 'email',
          'readable' => 'Your e-mail address',
          'type' => 'text',
          'htmltag' => 'email',
          'placeholder' => 'foo@bar.org',
         ],

    3 => ['id' => 'subject',
          'name' => 'subject',
          'readable' => 'Subject of your message',
          'type' => 'text',
          'htmltag' => 'input',
          'placeholder' => 'Subject of your message',
         ],

    4 => ['id' => 'body',
          'name' => 'body',
          'readable' => 'Type your message here',
          'type' => 'text',
          'htmltag' => 'textarea',
          'placeholder' => 'Type your message here',
    ]

];

// Filters for pattern matching, no need for e-mail because 'FILTER_VALIDATE_EMAIL' is already nicely doing it
$filter_names = "/^\p{Latin}+((?:-|\h)\p{Latin}+)*$/";
$filter_prefix = "/[\p{nD}\p{Latin}\p{P}\p{S}\h]/g";
