<?php

/*
 * This file is a default example, you NEED to renames it to 'variables.php', at the same location: 'src/var/'.
 * From your document root location you can safely do:
 * "$ mv srv/var/variables.example.php src/var/variables.php"
 *
 * A copy of this file can be found here:
 * - https://github.com/BarbeRousseLibre/hermessenger/blob/master/src/var/variables.example.php
 *
 * This file should not being shared (like on a public git's repository, for example) once it has been renamed. This
 * won't be considered as a big security issue, but, it gives locations used inside your server, which is not a good
 * habit. As it should always being added, once renamed to variables.php, to the .gitignore file as well.
 *
 * This file is used to allow administrator to install and configure Hermessenger.
 *
 * It gives locations to your document root, and inside it all needed sub-directories for Hermessenger to works.
 *
 * Some values are expected to never be changed, until you needs it and know why you needs it. It is supposed to works
 * out-of-the-box for most of them, at least $document_root has to be changed.
 *
 * Also note that you should not add a slash ("/") at the end or begining of any paths.
 *
 * The interesting part you should check out are:
 *
 * $document_root: The location of the website you want to add Hermessenger web form.
 *
 * $timezone: See https://www.php.net/manual/en/timezones.php for allowed timezone values, regarding the location.
 *
 */

/* - * - * - * - * - * CHANGE THIS ACCORDING YOUR OWN NEED - * - * - * - * - * - * - * - * - * - * - * - * - * - * - */

/*
 * Change the $document_root variable accordingly to your own configuration.
 *
 * This is the only mandatory value to change. Check out $timezone as well, you probably needs it too.
 *
 */
$document_root = "/CHANGE/ME/TO/YOUR/DOCUMENTROOT";

// Timezone to use, per default Europe/Paris, change according your location / need.
$timezone = "Europe/Paris";

// Needed sub-directories, these are safe to change before actually using Hermessenger but are made to works as it is.
$temp_mail_directory = "temp_mail_directory";
$mail_dir = "mail_dir";
$accepted_mail_dir = "ACCEPTED";
$rejected_mail_dir = "REJECTED";
$quarantine_dir = "quarantine";

/* WIP
 * Define the quarantine locations for suspicious mail request sending, too fast request, etc. As logs for them.
 *
 * "suspicious" is used for mails that was allowed, but a copy is written here (for, as example, later analysis).
 *
 * "too_fast" is used for mails that was sended too fast, see 'is_request_too_fast()' into src/functions.php for more
 * details.
 *
 * "rejected" is for mails that was not sended, but if a copy was asked from the administrator while using
 * 'is_request_too_fast()' function, see src/functions.php for more details.
 *
 * "logs" contains a plaintext file were all "suspicious", "too_fast" & "rejected" are logged into a log file for each
 * request.
 *
 */
$quarantine = "quarantine";
$suspicious = "suspicious";
$too_fast = "too_fast";
$rejected = "rejected";
$logs = "logs";

/* - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - */

/* These below are safe to change, you probably needs to change them until a better configuration system is added. WIP.
 *
 * Add, removes or modify keys and values accordingly to your need.
 *
 * If you want to modify the range for allowed values from the input's data of the user, modify $field_len_list.
 *
 * If you need to add, removes or modify an HTML input/textarea field, changes $mail_form.
 *
 */

/*
 * Define the range of each field between a *_min & *_max key's value. Default values are considered safe for Latin
 * alike language. You can probably keep them as it is.
 *
 */
$field_len_list = ['firstname_min' => 2, 'firstname_max' => 32,
                   'secondname_min' => 2, 'secondname_max' => 32,
                   'email_min' => 8, 'email_max' => 32,
                   'subject_min' => 8, 'subject_max' => 48,
                   'body_min' => 64, 'body_max' => 2048
                  ];

/* WIP
 * Define the range for adding a prefix for subject & body field once the mail is sended.
 *
 * THIS WAS DISABLED UNTIL THE FEATURE IS IMPLEMENTED PROPERLY.
 *
 */
/*$prefix_len = ['prefix_subject_min' => 1, 'prefix_subject_max' => 16,
               'prefix_body_min' => 1, 'prefix_body_max' => 512,
              ];*/

/*
 * Each row/key define a new input/textarea field in the HTML code.
 *
 * Per default, allows these fields:
 *
 * 'firstname', 'secondname', 'email', 'subject' & 'body'. You can add, removes or modify them as needed.
 *
 * Each one of them use the 'id' and 'name' used in the actual HTML code in the web mail form. Be sure to use properly
 * these values, otherwise Hermessenger would react weirdly, or not work at all, rejecting all request no matter what.
 *
 * Each one of them gives a 'readable', 'type', 'htmltag' and 'placeholder' value. You can / have to change them
 * regarding your actual HTML code.
 *
 * For more details:
 *
 * <input/>: https://developer.mozilla.org/fr/docs/Web/HTML/Element/input
 * <textarea></textarea>: https://developer.mozilla.org/fr/docs/Web/HTML/Element/textarea
 *
 */
$mail_form = [
    0 => ['id' => 'firstname',
          'name' => 'firstname',
          'readable' => 'First name',
          'type' => 'text',
          'htmltag' => 'input',
          'placeholder' => 'Jean'
         ],

    1 => ['id' => 'secondname',
          'name' => 'secondname',
          'readable' => 'Second name',
          'type' => 'text',
          'htmltag' => 'input',
          'placeholder' => 'Dupont'
         ],

    2 => ['id' => 'email',
          'name' => 'email',
          'readable' => 'Your e-mail address',
          'type' => 'text',
          'htmltag' => 'email',
          'placeholder' => 'foo@bar.org'
         ],

    3 => ['id' => 'subject',
          'name' => 'subject',
          'readable' => 'Subject of your message',
          'type' => 'text',
          'htmltag' => 'input',
          'placeholder' => 'Subject of your message'
         ],

    4 => ['id' => 'body',
          'name' => 'body',
          'readable' => 'Type your message here',
          'type' => 'text',
          'htmltag' => 'textarea',
          'placeholder' => 'Type your message here'
    ]

];

/* Filters for pattern matching, no need for e-mail because 'FILTER_VALIDATE_EMAIL' is already nicely doing it.
 *
 * Only changes these if it's needed.
 */
$filter_names = "/^\p{Latin}+((?:-|\h)\p{Latin}+)*$/"; // Allow latin, separated by white-space/NBSP or hypen ("-")
// WIP
// $filter_prefix = "/[\p{nD}\p{Latin}\p{P}\p{S}\h]/g"; // Allow latin and other common characters such as hypen, etc.

/* - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - */

// These below should probably never be changed ! Do it at your own risk.

// Set internal character encoding to UTF-8.
mb_internal_encoding("UTF-8"); // This is probably not a very good idea to change this ! Do it if you NEEDS it.

/*
 * Per default, define there is no copy to send.
 *
 * Do not change this ! This will probably break the code.
 *
 */
$is_receipt_asked = false; // UNSAFE TO MODIFY THIS MANUALLY
// ^ This is probably better to use a constant, and works on a copy. WIP.

$document_root = $document_root . "/"; // Really don't touch this

// These are not necessary to be changed and could break Hermessenger. Do it at your own risk.
$locations = ["pending_mails"           => $document_root . $temp_mail_directory . "/",
              "logs_mail"               => $document_root . $mail_dir . "/",
              "logs_mail_accepted"      => $document_root . $mail_dir . $accepted_mail_dir . "/",
              "logs_mail_rejected"      => $document_root . $mail_dir . $rejected_mail_dir . "/",
              "quarantine"              => $document_root . $quarantine . "/"
];

$quarantine_locations = ["suspicious"   => $locations["quarantine"] . $suspicious . "/",
                         "too_fast"     => $locations["quarantine"] . $too_fast . "/",
                         "rejected"     => $locations["quarantine"] . $rejected . "/",
                         "logs"         => $locations["quarantine"] . $logs . "/"
];
// $quarantine_log_file = $quarantine_locations["logs"] . "todefinebaby"; // WIP, see this a bit later.

/* - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - */
