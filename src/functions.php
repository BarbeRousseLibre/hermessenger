<?php 

/* Set internal character encoding to UTF-8 */
mb_internal_encoding("UTF-8");

require_once 'var/variables.php';

/*
 * Reject all domains used in the form as 'e-mail' if it is listed as a non-trusty, disposable e-mail domains from such
 * service.
 *
 * $user_email is a string from the $_POST of the user.
 *
 * If the mail domain is untrusty, report true (it's untrusty), otherwise false (it's trusty regarding the list).
 *
 */
function reject_disposable_email_domain($user_email) {

    require 'var/untrusty_domains/disposable_email_domains.php';

    // Removes username from $user_email
    $matches = [];
    preg_match("/@(.+\..*$)/", $user_email, $matches);

    // Merge all sub-array into one
    $all_disposable_mails_domains = array_merge(...array_values($non_trusty_esp_domain));

    return (bool) (in_array($matches[1], $all_disposable_mails_domains));

}

/*
 * Once the mail has been given to PHPMailer, stores it regarding if PHPMailer returned true or false.
 * 'rejected_mail' goes into 'mail_dir/REJECTED' subdirectory, and 'accepted_mail' into 'mail_dir/ACCEPTED'.
 *
 * Move the mail from $pending_mail_path's directory to the subdirectory (see above).
 *
 * $pending_mail_path is the location where holding mails are waiting before being send.
 *
 * $status define where the mail should go (ACCEPTED/REJECTED).
 *
 * $locations is the array with all needed locations to retrieve and move around the mail's file.
 *
 * Return true or false.
 *
 */
function store_sended_mail_to_logs($pending_mail_path, $status, $locations) {

    // Define the prefix to use
    $prefix = (!$status) ? "rejected_mail" : "accepted_mail" ;

    // Extract the current name of the file on the whole path, matching everything after last slash ('/') (non-included)
    $file_current_name = substr($pending_mail_path, strrpos($pending_mail_path, '/' ) + 1);

    // Replace the file's prefix by the status of the sending
    $file_name = str_replace("mail_pending", $prefix, $file_current_name);

    // Define the full path for the file (so the name is included)
    $full_path_to_moved_file = $locations . $file_name;

    // Move the file from the pending mail directory to the target directory (ACCEPTED or REJECTED)
    return (bool) rename($pending_mail_path, $full_path_to_moved_file);

}

/*
 * From the path given as argument, return only the oldest file, using scandir.
 *
 * $temp_mail_dir should be the location of the holding queue directory for mail.
 *
 * It's expected to clean 3 entry from the scanned directory: '.', '..' and '.gitkeep'.
 *
 * Return an array with all data's from the mail.
 *
 */
function return_oldest_mail($temp_mail_dir) {

    // Retrieve all files in the temp directory as an array
    $files_list_uncleaned = scandir($temp_mail_dir);

    // Clean list to remove all unwanted files, such as '.', '..' or '.gitkeep', making index 0 the oldest file.
    $cleaned_files_list = array_slice($files_list_uncleaned, 3);

    // Once cleaned, the first index (0) is the oldest file. Scandir()'s already sorting by default from oldest->newest.
    $oldest_file_index = 0;

    if (!array_key_exists($oldest_file_index, $cleaned_files_list)) { // If no mail to send.

        return false;

    } else { // There is a mail to send

        $oldest_mail = $temp_mail_dir . $cleaned_files_list[$oldest_file_index];

    }

    // Retrieve in the file the desired field, delimited by '|'
    $mail_file = @fopen($oldest_mail, 'r');
    $cgi_mail_data = ['firstname',
                      'secondname',
                      'email',
                      'subject',
                      'body',
                      'pending_mail_location',
                      'mail_file_name'
                     ];

    // Read the file, line by line, until it get false (no more line)
    while (($line = fgets($mail_file)) !== false) {

        $line = explode("|", $line);

        $cgi_mail_data[$line[2]] = $line[3];

    }
    fclose($mail_file);

    // Define the temporary pending queue directory for e-mails
    $cgi_mail_data["pending_mail_location"] = $temp_mail_dir;

    // Define the file's name of the oldest mail
    $cgi_mail_data["mail_file_name"] = $cleaned_files_list[$oldest_file_index];

    return $cgi_mail_data;
}

/* Take the mail from the the form and stores it into $pending_mail_directory. The mail will get formated this way
 * for each row:
 *
 * |lineNumber|htmlTag|userInput|
 *
 * Exception being made for the three last row:
 *
 * |date_and_time| being the date & time when the user hit the send e-mail button on the page.
 *
 * |IP| being the client's IP. Important: This data isn't trusty, but can be useful for stats and such things.
 *
 * |send_copy| being a boolean stating if a copy has to be send to the actual user (|email|) as a recipient.
 *
 * These can be used to find a bit more data about who's trying to send an e-mail and when. Remembering that IP
 * is an untrusty information that can be hidden easily.
 *
 * Each file will then be formated this way (example regarding how many fields are in the form):
 *
 * |0|firstname|foo|
 * |1|secondname|bar|
 * |2|email|foo.bar@toto.org|
 * |3|subject|This is a test message|
 * |4|body|Body of the message|
 * |5|date_and_time|YY-MM-DD_HHMMSS|
 * |6|IP|XX.XXX.XXX.XX|
 * |7|send_copy|bool|
 *
 * The mail's file will be named this way:
 *
 * 'mail_pending_DATE_TIME_IP_firstname_secondname_email_at_domain_tld.txt'
 *
 * Where all white space characters are replaced by '_' and arobase ('@') is replaced by '_at_'.
 *
 * This way, both file's name and it's content could be used to extract data from these.
 *
 * $mail is the array from $post_copy after remove_receipt_key() has cleaned it, if present.
 *
 * $pending_mail_directory is the path of the directory that will hold the file (.txt) used to store the mail until it
 * is send by a cronjob task running 'php_mailer.php' file every X minutes/hours/day (etc), or a loop.
 *
 * $send_copy is a bool, stating if a second copy has to be send to the user of the form / sender. Is turned into a
 * string for convinience.
 *
 * Store the mail into the directory if true, otherwise false.
 *
 */
function store_to_plaintext($mail, $pending_mail_directory, $send_copy) {

    // Extra infos to adds to the file's name and it's content (statistic from user) at the end of the file's content
    $extra_info = [
        'IP'                => $_SERVER['REMOTE_ADDR'],
        'date_and_time'     => date('Y-m-d_His'),
        'send_copy'         => ($send_copy) ? "true" : "false"
    ];

    // Merging the $_POST from user, cleaned, with IP, date and time from request
    $raw_content = array_merge($mail, $extra_info);

    // Start composing the file's name, before non-wanted characters are removed
    $file_name = "mail_pending_" .
                 $raw_content['date_and_time'] .
                 "_" .
                 $raw_content['IP'] .
                 "_" .
                 $raw_content['firstname'] .
                 "_" .
                 $raw_content['secondname'] .
                 "_" .
                 $raw_content['email'] .
                 ".txt";

    // Replace '@' with '_at_', this is not ok otherwise
    $file_name_without_at = str_replace("@", "_at_", $file_name);
    // Replace any space by an underscore, for easier manipulation in shell (beside being legal and correct)
    $file_name_clean = preg_replace('/\s+/', '_', $file_name_without_at);
    // Prepare the name of the file and it's path
    $full_file_path = $pending_mail_directory . $file_name_clean; // Append the directory's path to the file name

    // Write down the mail's file to the pending queue directory, before sending
    $line_number = 0;
    foreach ($raw_content as $field => $data) { // Write the fill line by line

        file_put_contents($full_file_path, "|$line_number|$field|$data|\n", FILE_APPEND | LOCK_EX);
        $line_number++;

    }

    return file_exists($full_file_path);

}

/* Return true if $string is matching lenght against allowed range (>=$min, <= $max) and pattern matching (PCRE2).
 * See $filter_type below for more details about pattern matched.
 *
 * If not valid regarding the conditions made, return false and skip the following execution of any useless code:
 *
 * No more tests are needed at this point. Be aware that front-end part should has a method to deny any bad formatted
 * (pattern, lenght) mail sending request.
 *
 * This function should be a safety-nest against user's error, front-end developper error or a forgetfulness from them
 * or worst, a fake user (as bot or AI), or any attack with lenght or pattern, if any.
 *
 * If $filter_type isn't used with one of the value below, then it return false.
 *
 * All args are mandatory.
 *
 * $string: Content to check against regex pattern or PHP filters.
 *
 * $filter_type should be one of these only:
 *
 * - names ($filter_names) : Accept all Latin-Unicode characers plus (severals) hyphen for composed names, unicode.
 *
 * - email (FILTER_VALIDATE_EMAIL): Following filter_var's filters, see:
 * https://www.php.net/manual/en/filter.filters.validate.php
 *
 * - text ($filter_text) : Accept all Latin-Unicode characters, plus sign and usual and commons non-alphabetic and
 * non-numeric, as it match numeric characters too, space, tabs, etc.
 *
 * Please see PCRE2 patterns and syntax specifications :
 * - https://www.pcre.org/
 *
 * It's expected to receive Latin-alike unicode input, not cyrilic or asians characters, hindie, etc.
 * WIP: FEATURE TO ADD LATER.
 *
 * $min is the minimum allowed value (included) for the string's lenght.
 *
 * $max is the maximum allowed value (included) for the string's lenght.
 *
 */
function check_string_validity($string, $filter_type, $min, $max) {

   // Range check
   if (strlen($string) < $min OR strlen($string) > $max) {

       return false;

   } else { // If lenght is in range, check pattern against $filter's rules

       // Pattern check
       switch ($filter_type) {

           case 'names':
               return (bool) preg_match("/^\p{Latin}+((?:-|\h)\p{Latin}+)*$/", $string);

           case 'email':
               return (bool) filter_var($string, FILTER_VALIDATE_EMAIL);

           case 'text':
               return (bool) preg_match("/^[\p{Latin}\p{Common}\d\s\p{P}\p{S}]+$/u", $string);

           default: return false; // Not a valid filter

        }
    }
}

/* From $user_post (being $_POST) and against $allowed_len_list, as pattern matching, define if the
 * email that the user is trying to send is valid regarding the rules of the script.
 *
 * Use check_string_validity() to test the user's posted value.
 *
 * Return true if every checks are OK.
 *
 * Otherwise, return false and no more tests are needed, if one has failed, the mail won't be sended anyway.
 *
 */
function validate_email_sending($user_post, $allowed_len_list_min, $allowed_len_list_max, $field_type_list) {

    // For each itteration, define what are min and max values for each field, as the filter to run against the data
    foreach ($user_post as $key => $value) {

        $min = $allowed_len_list_min[$key];
        $max = $allowed_len_list_max[$key];
        $current_filter = $field_type_list[$key];

        if (!check_string_validity($value, $current_filter, $min, $max)) {

            return false;

        }

    }

    return true;

}
