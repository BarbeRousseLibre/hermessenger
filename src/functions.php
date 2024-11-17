<?php 

require_once(__DIR__ . '/../config/variables.php');

/* Set internal character encoding to UTF-8 */
mb_internal_encoding($char_encoding);

/*
 * Return to the HTML page defined as argument ($target).
 *
 * $target should be the full URL to the futur location:
 * scheme://domain-name/ressource_path
 *
 *
 * If extra parameters was added into ' config/variables.php ' for ' $redirecting_locations["extra_parameters"] ',
 * then it is append at the end of the $target:
 *
 * scheme://domain-name/ressource_path?key1=value1&key2=value2
 *
 *
 * If an anchor is needed, add it into ' config/variables.php ' for ' $redirecting_locations["anchor"] ', giving:
 *
 * scheme://domain-name/ressource_path#SomewhereInTheRessource
 *
 *
 * Or with both (extra parameters and anchor):
 *
 * scheme://domain-name/ressource_path?key1=value1&key2=value2#SomewhereInTheRessource
 *
 *
 * Before actually sending the new location (redirection), checks if no headers was sent before.
 *
 * Return false if a headers was already sent, otherwise silently exit once the client has been redirected.
 *
 */
function redirect_browser_to_new_location($target) {

    if (!headers_sent()) {

        // Redirect the client to the $target
        header('Location: ' . $target);

        exit();

    } else {

        return false;

    }

}


/*
 * Reject all domains used in the form as 'e-mail' if it is listed as a non-trusty, disposable e-mail domains from such
 * service.
 *
 * $user_email is a string from the $_POST of the user.
 *
 * If the mail domain is untrusty, report true (it's untrusty), otherwise false (it's trusty regarding the list).
 *
 * If the e-mail is misformated (for example, not 'at' ('@') character) return false as well.
 * WIP: This is not very good at it is.
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
 * From the $mail argument being the copy of $_POST, after it was cleaned from receipt key and honey-pot field, write
 * into $pending_mail_directory the file in JSON, without forgetting to state if a copy was asked (since it was
 * removed from the $mail, and saved in a variable for this function call, being the third argument).
 *
 * $mail is an array, copied from $_POST.
 *
 * $pending_mail_directory, the current directory that will write the pending mail file in JSON.
 *
 * $send_copy, a bool extracted from the original $_POST, unset while being copied into a variable.
 *
 * $target, a string or a bool. If not used (per default is false (bool)), does not ask for redirection. Otherwise
 * should be a string used as a new location for redirecting the browser once the request has been made.
 *
 * Returns nothing, write down the file.
 *
 */
function store_to_json($mail, $pending_mail_directory, $send_copy, $target = false) {

    // Extra infos to adds to the file's name and it's content (statistic from user) at the end of the file's content
    $extra_info = [
        'IP'                => $_SERVER['REMOTE_ADDR'],
        'date_and_time'     => date('Y-m-d_His'),
        'send_copy'         => ($send_copy) ? true : false
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
                 ".json";

    // Replace '@' with '_at_', this is not ok otherwise
    $file_name_without_at = str_replace("@", "_at_", $file_name);

    // Replace any space by an underscore, for easier manipulation in shell (beside being legal and correct)
    $file_name_clean = preg_replace('/\s+/', '_', $file_name_without_at);

    // Prepare the name of the file and it's path
    $full_file_path = $pending_mail_directory . $file_name_clean; // Append the directory's path to the file name

    // Convert the raw content into JSON
    $json_content = json_encode($raw_content, JSON_PRETTY_PRINT);

    // Write down the file
    file_put_contents($full_file_path, $json_content, FILE_APPEND | LOCK_EX);

    // If a redirectiong location has been set, redirect the browser to it
    if ($target !== false) {

        redirect_browser_to_new_location($target);

    }


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
 * $filter_type is a string and should be one of these only:
 *
 * - names: Accept all Latin-Unicode characers plus (severals) hyphen for composed names, unicode.
 *
 * - email (FILTER_VALIDATE_EMAIL): Following filter_var's filters, see:
 * https://www.php.net/manual/en/filter.filters.validate.php
 *
 * - text: Accept all Latin-Unicode characters, plus sign and usual and commons non-alphabetic and
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
   if (strlen($string) < $min) {

       return false;

   }

   if (strlen($string) > $max) {

       return false;

   }

    // Pattern check
    switch ($filter_type) {

        case "names":
            return (bool) preg_match("/^\p{Latin}+((?:-|\h)\p{Latin}+)*$/u", $string);

        case "email":
            return (bool) filter_var($string, FILTER_VALIDATE_EMAIL);

        case "text":
            return (bool) preg_match("/^[\p{Latin}\p{Common}\d\s\p{P}\p{S}]+$/u", $string);

        default: return false; // Not a valid filter

    }

}

/* From $user_post (being a clean copy of $_POST) and against $allowed_len_list_min and $allowed_len_list_max, as
 * pattern matching from $field_type_list, define if the email that the user is trying to send is valid regarding the
 * rules of the script.
 *
 * Use check_string_validity() to test the user's posted value, if it return false, this function will return false too.
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

        $is_current_field_valid = check_string_validity($value, $current_filter, $min, $max);
        if (!$is_current_field_valid) {

            return false;

        }

    }

    return true;

}
