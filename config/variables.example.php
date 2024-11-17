<?php

/*
 * This file is a default example, you NEED to renames it to 'variables.php', at the same location: ' src/var/ '.
 * From your document root location you can safely do (please adapt this command):
 * "$ cp srv/var/variables.example.php src/var/variables.php"
 *
 * A copy of this file can be found here:
 * - https://github.com/BarbeRousseLibre/hermessenger/blob/master/src/var/variables.example.php
 *
 * This file should not being shared (like on a public git's repository, for example) once it has been renamed. This
 * won't be considered as a big security issue, but, it gives locations used inside your server, which is not a good
 * habit. As it should always being added, once renamed to ' variables.php ', to the ' .gitignore ' file as well, if
 * it is needed.
 *
 * This file is used to allow administrator to install and configure Hermessenger.
 *
 * It gives locations to your document root, and inside it all needed sub-directories for Hermessenger to works.
 *
 * Some values are expected to never be changed, until you needs it and know why you needs it. It is supposed to works
 * out-of-the-box for most of them, but at least $document_root has to be changed!
 *
 * Also note that you should not add a slash ("/") at the end or begining of any paths.
 *
 * The interesting part you should check out are:
 *
 * $document_root_parent: The location of the website you want to add Hermessenger web form.
 *
 * $timezone: See https://www.php.net/manual/en/timezones.php for allowed timezone values, regarding the location.
 *
 * $redirecting_location: Add here the needed info for the redirection after the client has made the request.
 *
 */

/* - * - * - * - * - * CHANGE THIS ACCORDING YOUR OWN NEED - * - * - * - * - * - * - * - * - * - * - * - * - * - * - */

/*
 * Change the $document_root_parent variable accordingly to your own configuration.
 *
 * This is the only mandatory value to change. Check out $timezone as well, you probably needs it too.
 *
 */
$document_root_parent = ""; // NO SLASH AT THE END OF PATH !

// Timezone to use, per default Europe/Paris, change according your location / need.
$timezone = "Europe/Paris";

/*
 * Encoding needed, please see for allowed value:
 * - https://www.php.net/manual/en/mbstring.supported-encodings.php
 *
 * Default to UTF-8, which you probably need anyway.
 *
 */
$char_encoding = "UTF-8";

// Needed sub-directories, these are safe to change before actually using Hermessenger but are made to works as it is.
$temp_mail_directory = "temp_mail_directory";
$mail_dir = "mail_dir";
$accepted_mail_dir = "ACCEPTED";
$rejected_mail_dir = "REJECTED";
$disposable_mail_dir = "UNTRUSTY/DISPOSABLE";


/*
 * Define the targeted full URL for redirecting the browser once client has sended it's mail request.
 *
 * "scheme" should be "http" or "https".
 *
 * "domain_name" should be the domain hosting the ressource path, see below, ending with the top-level-domain
 * ('.com', '.net', etc).
 *
 * "ressource_path" is the name of the targeted file used for redirecting the browser.
 *
 * Do NOT use any character pattern like ' :// ' or ' / ' (slash) to make any delimiter. This is made later in this
 * file and you should NOT edit this.
 *
 */
$redirecting_location = ["scheme"             => "",
                         "domain_name"        => "",
                         "ressource_path"     => ""];

/* - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - */

/* These below are safe to change, until a better configuration system is added. WIP.
 *
 * If you want to modify the range for allowed values from the input's data of the user, modify $field_len_list.
 *
 * You should avoid adding or removing <input/> or <textarea></textarea> here until Hermessenger could take care of it.
 * This is a WIP for 0.2.
 *
 */

/*
 * Define the range of each field between a *_min & *_max key's value. Default values are considered safe for Latin
 * alike language. You can probably keep them as it is.
 *
 */
$field_len_list_min = [
      'firstname' => 2,
      'secondname' => 2,
      'email' => 8,
      'subject' => 8,
      'body' => 64];

$field_len_list_max = [
      'firstname' => 32,
      'secondname' => 32,
      'email' => 48,
      'subject' => 48,
      'body' => 2048];

$field_type = [
      'firstname' => 'names',
      'secondname' => 'names',
      'email' => 'email',
      'subject' => 'text',
      'body' => 'text'];

/*
 * Each row/key define a new input/textarea field in the HTML code:
 *
 * 'firstname', 'secondname', 'email', 'subject' & 'body'.
 *
 * As it is for now, 0.1.* it is not possible to add or remove these values, WIP for 0.2.
 *
 * Each one of them use the 'id' and 'name' used in the actual HTML code in the web mail form.
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
          'placeholder' => 'Jean'],

    1 => ['id' => 'secondname',
          'name' => 'secondname',
          'readable' => 'Second name',
          'type' => 'text',
          'htmltag' => 'input',
          'placeholder' => 'Dupont'],

    2 => ['id' => 'email',
          'name' => 'email',
          'readable' => 'Your e-mail address',
          'type' => 'text',
          'htmltag' => 'email',
          'placeholder' => 'foo@bar.org'],

    3 => ['id' => 'subject',
          'name' => 'subject',
          'readable' => 'Subject of your message',
          'type' => 'text',
          'htmltag' => 'input',
          'placeholder' => 'Subject of your message'],

    4 => ['id' => 'body',
          'name' => 'body',
          'readable' => 'Type your message here',
          'type' => 'text',
          'htmltag' => 'textarea',
          'placeholder' => 'Type your message here']

];

/* - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - */

// These below should probably never be changed ! Do it at your own risk.

// Set internal character encoding to UTF-8.
mb_internal_encoding($char_encoding); // See top of the file to change this

// Locations used to store mail's file (JSON)
$locations = ["pending_mails"           =>
                  $document_root_parent .
                  "/var/" .
                  $temp_mail_directory .
                  "/",

              "logs_mail"               =>
                  $document_root_parent .
                  "/var/" .
                  $mail_dir .
                  "/",

              "logs_mail_accepted"      =>
                  $document_root_parent .
                  "/var/" .
                  $mail_dir .
                  "/" .
                  $accepted_mail_dir .
                  "/",

              "logs_mail_rejected"      =>
                  $document_root_parent .
                  "/var/" .
                  $mail_dir .
                  "/" .
                  $rejected_mail_dir .
                  "/",

              "logs_mail_disposable"    =>
                  $document_root_parent .
                  "/var/" .
                  $mail_dir .
                  "/" .
                  $disposable_mail_dir .
                  "/"
              ];

// Build the full targeted URL for redirection after sending the mail request
$target_for_redirection = $redirecting_location["scheme"] .
                          "://" .
                          $redirecting_location["domain_name"] .
                          "/" .
                          $redirecting_location["ressource_path"];

/* - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - */
