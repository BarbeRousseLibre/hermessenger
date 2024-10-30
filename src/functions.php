<?php 

/* Set internal character encoding to UTF-8 */
mb_internal_encoding("UTF-8");

require_once 'var/variables.php';

/* WIP */
//function block_non_trusty_domain($domain, $blocked_domain_list) {
    /*
     * Check if the mail domain from the user input is part of a non-trusty / disposable ESP and refuse them.
     *
     * $domain is a string and should be the domain, after arobase ('@').
     *
     * Return true if not part of the list, otherwise return false.
     *
     */

    /*foreach ($blocked_domain_list as $esp => $listed_domain) {

        foreach ($listed_domain as $current_domain) {

            if (in_array($domain, $current_domain)) {

                return false;

            }

        }

    }

}*/

function store_sended_mail_to_logs($pending_mail_path, $status, $locations) {
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

    /*
     * State if it's a rejected or accepted mail (from phpmailer return code) and will use it to replace "pending_mail"
     * by 'rejected_mail' or 'accepted_mail' into the name's file, defining also it's futur location.
     *
     */
    $mail_status = (!$status ? "rejected_mail" : "accepted_mail");
    $subdir_status_path = (!$mail_status) ? $subdir_status_path = $locations["logs_mail_rejected"] :
                                            $subdir_status_path = $locations["logs_mail_accepted"];

    // Extract only the name of the file on the whole path, checking for everything after last slash ('/')
    $final_file_name = substr($pending_mail_path, strrpos($pending_mail_path, '/' ) + 1);

    // Replace the file's prefix by the status of the sending
    $final_file_name = str_replace("mail_pending", $mail_status, $final_file_name);

    // Append to the directory path the name of the file.
    $final_file_name = $subdir_status_path . $final_file_name;

    // Move the file from the pending mail directory to the target directory (ACCEPTED or REJECTED)
    if (!rename($pending_mail_path, $final_file_name)) {

        return false;

    } else {

        return true;

    }

}

function return_oldest_mail($temp_mail_dir) {
    /*
     * From the path given as argument, return only the oldest file, using scandir.
     *
     * $temp_mail_dir should be the location of the holding queue directory for mail.
     *
     * Return an array with all data's from the mail.
     *
     * scandir's default behavior allow this to directly get as third index (first being '.', second being '..'), being
     * the oldest file.
     *
     */

    // Retrieve all files in the temp directory as an array
    $files = scandir($temp_mail_dir);

    // scandir() return per default the oldest file as first in list, after '.' and '..'. Index being 2 for the first.
    $oldest_mail = $temp_mail_dir . $files[2];

    // Retrieve in the file the desired field, delimited by '|'
    $mail_file = @fopen($oldest_mail, 'r');
    $cgi_mail_data = array('firstname',
                           'secondname',
                           'email',
                           'subject',
                           'body',
                           'pending_mail_location',
                           'mail_file_name'
    );

    // Read the file, line by line, until it get false (no more line)
    while (($line = fgets($mail_file)) !== false) {

        $line = explode("|", $line);

        $cgi_mail_data[$line[2]] = $line[3];

    }
    fclose($mail_file);

    // Define the temporary pending queue directory for e-mails
    $cgi_mail_data["pending_mail_location"] = $temp_mail_dir;

    // Define the file's name of the oldest mail
    $cgi_mail_data["mail_file_name"] = $files[2];

    return $cgi_mail_data;
}

function store_to_plaintext($mail, $temp_mail_dir, $send_copy) {
    /* Take the mail from the the form and stores it into $temp_mail_dir. The mail will get formated this way for each
     * row:
     *
     * |lineNumber|htmlTag|userInput|
     *
     * Exception being made for :
     *
     * - Index 5 (6th row): |date_and_time| being the date & time when the user hit the send e-mail button on the page.
     *
     * - Index 6 (7th row): |IP| being the client's IP. Important: This data isn't trusty, but can be useful for stats and
     *                      such things.
     *
     * - Index 7 (8th row): |send_copy| being a boolean stating if a copy has to be send to the actual user (|email|) as
     *                      a recipient.
     *
     * These can be used to find a bit more data about who's trying to send an e-mail and when. Remembering that IP
     * is an untrusty information that can be hidden easily.
     *
     * Each file will then be formated this way:
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
     * $mail is the array from $_POST after remove_receipt_key() has cleaned it, if present.
     *
     * $temp_mail_dir is the path of the directory that will hold the file (.txt) used to store the mail until it is
     * send by a cronjob task running 'php_mailer.php' file every X minutes/hours/day (etc), or a loop.
     *
     * $send_copy is a bool, stating if a second copy has to be send to the user of the form / sender.
     *
     * Store the mail into the directory if true and returns it.
     *
     * Otherwise, return false.
     *
     */

    // Define an array where each key is an user input or other informations regarding the mail sending
    $FROM_post_info = array(
        'username'      =>      $_POST['email'],
        'firstname'     =>      $_POST['firstname'],
        'secondname'    =>      $_POST['secondname'],
        'name'          =>      $_POST['firstname'] . " " . $_POST['secondname'],
        'IP'            =>      $_SERVER['REMOTE_ADDR'], // Client's IP while hitting the send e-mail button
        'date_and_time' =>      date('Y-m-d_His'), // Client's date and time while hitting the send e-mail button
        'send_copy'     =>      $send_copy
    );

    // Format the date & time information as IP for the file's name
    $date_and_time = $FROM_post_info['date_and_time'];
    $IP = $FROM_post_info['IP'];
    $miscellaneous = $date_and_time . "_" . $IP;

    // How the file for the mail is named
    $temp_mail_file = "mail_pending_" .
                       $FROM_post_info['date_and_time'] . "_" .
                       $FROM_post_info['IP'] . "_" .
                       $FROM_post_info['firstname'] . "_" .
                       $FROM_post_info['secondname'] . "_" .
                       $FROM_post_info['username'] .
                       ".txt";

    // Where the file is stored
    $temp_mail_file = $temp_mail_dir . $temp_mail_file;

    // Replace any space by an underscore
    $temp_mail_file = preg_replace('/\s+/', '_', $temp_mail_file);

    // Replace '@' with '_at_'
    $temp_mail_file = str_replace("@", "_at_", $temp_mail_file);

    $line = 0; // Define a counter used to number lines
    foreach ($mail as $field => $user_input) {

        switch ($field) {

            // WIP
            // case "nickname": break; // Remove honey-pot field, probably could do it earlier / better

            case "firstname" || "secondname" || "email" || "subject" || "body": // User input's data

                file_put_contents($temp_mail_file, "|$line|$field|$user_input|\n", FILE_APPEND | LOCK_EX);
                $line++;

                if ($field == "body") { // Once all the fields from the user input has been written

                    // Write down date and time row
                    file_put_contents($temp_mail_file, "|$line|date_and_time|$date_and_time|\n", FILE_APPEND | LOCK_EX);
                    $line++;

                    // Write down IP row
                    file_put_contents($temp_mail_file, "|$line|IP|$IP|\n", FILE_APPEND | LOCK_EX);
                    $line++;

                    // Is a copy asked by user ?
                    $send_copy === true ? $send_copy = "true" : $send_copy = "false";

                    // Write down the switch stating a second mail has to be send as a copy or not
                    file_put_contents($temp_mail_file, "|$line|send_copy|$send_copy|", FILE_APPEND | LOCK_EX);

                    return $temp_mail_file;

                }

                break;

            default: return false;

        }

    }

}

function remove_receipt_key($is_present) {
    /*
     * If user has checked the checkbox 'receive_ack_receipt', removes it and return 'true'.
     *
     * $is_present is a bool.
     *
     * Return true, never false because this function is called IF it's supposed to get this key and it's necessary to
     * removes it.
     *
     * WARN: This function could be overkill ?
     *
     */

    unset($_POST['receive_ack_receipt']); // Remove this key to avoid conflict for testing validity of user input
    return true;

}

function check_string_validity($string, $filter_type, $min, $max) {
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

   // Range check
   if (strlen($string) < $min OR strlen($string) > $max) {

       return false;

   } else { // If lenght is in range, check pattern against $filter's rules

       // Pattern check
       switch ($filter_type) {

           case 'names':

               if (!preg_match($filter_names, $string)) {

                   return false;

               } else {

                   return true;

               }

           case 'email':

               if (!filter_var($string, FILTER_VALIDATE_EMAIL)) {

                   return false;

               } else {

                   return true;

               }

           case 'text':

               if (!preg_match("/^[\p{Latin}\p{Common}\d\s\p{P}\p{S}]+$/u", $string)) {

                   return false;

               } else {

                   return true;

               }

           default: return false; // Not a valid filter

        }
    }
}

function validate_email_sending($user_post, $allowed_len_list) {
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

    // Define at each iteration what are min and max value for range, as the filter to use for pattern matching.
    foreach ($user_post as $key => $value) {

        switch ($key) {

            case 'firstname':

                $min = $allowed_len_list['firstname_min'];
                $max = $allowed_len_list['firstname_max'];
                $current_filter = 'names';

                break;

            case 'secondname':

                $min = $allowed_len_list['secondname_min'];
                $max = $allowed_len_list['secondname_max'];
                $current_filter = 'names';

                break;

            case 'email':

                $min = $allowed_len_list['email_min'];
                $max = $allowed_len_list['email_max'];
                $current_filter = 'email';

                break;

            case 'subject':

                $min = $allowed_len_list['subject_min'];
                $max = $allowed_len_list['subject_max'];
                $current_filter = 'text';

                break;

            case 'body':

                $min = $allowed_len_list['body_min'];
                $max = $allowed_len_list['body_max'];
                $current_filter = 'text';

                break;

        }

    }

    if (!check_string_validity($value, $current_filter, $min, $max)) {

        return false;

    } else {

        return true;

    }

}
