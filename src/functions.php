<?php 

require_once(__DIR__ . '/../config/settings.php');
require_once(__DIR__ . '/../config/form_config.php');
require_once(__DIR__ . '/../config/server_path_config.php');

/* Set internal character encoding to UTF-8 */
mb_internal_encoding($char_encoding);

/*
 * Take the $_POST from user and do a serie of checks:
 *
 * - Is $_POST empty
 * - Is email domain input by user untrusty (not in the $non_trusty_esp_domain array)
 * - Is the honey-pot used
 * - Is the mandatory checkbox for rules has been checked or not
 * - Is the number of fields valid
 * - Is the length for each according field valid
 * - Is the pattern for each according field valid
 *
 * All above would redirect the user on the rejected sending page if true.
 *
 * It is also doing:
 * - Storing if a copy has to be send to the sender (e-mail address used while filling form)
 * - What is the used prefixed subject list
 *
 * These above are simply features that is not rejecting the message.
 *
 * $post has to be the $_POST or $_POST's copy (recommanded).
 *
 * $html_elements_list is the array used in ' form_config.php ' file, defining how many fields there are to test as
 * what are their respective rules (minimun and maximum lenght, their pattern, etc).
 *
 * $locations is the array defining which locations exist on the system to store pending mails, accepted or rejected,
 * untrusty, etc.
 *
 * At the first encountered error, return false, which should then once this function called, redirect on the rejected
 * sending page. The error will call the ' store_to_json() ' functions with " logs_mail_rejected " as location.
 *
 * If no error was find, then return true, which should then redirected the browser to the accepted sending page.
 * The file will then be stored into the "pending_mails" location for further sending.
 *
 * If all fields are valids BUT the honey-pot is used, then redirected the user on accepted sending page, while
 * actually not sending anything, hoping to trick even more bots and alike.
 *
 */
function checking_form($post, $html_elements_list, $locations) {

    $post_copy = $post; // Work on a copy of $post
    $is_receipt_asked = false; // Per default, none is asked
    $removed_keys = 0; // Count the number of keys that has been removed for a correct checks later on

    // Test if $_POST isn't empty
    if (count($post_copy) == 0) {

        store_to_json($post_copy, $locations["logs_mail_rejected"], $is_receipt_asked);
        return false;

    }

    // Test if the used domain mail by user is trusty, if not, return false and reject the request
    if ($is_domain_trusty = reject_disposable_email_domain($post_copy['email'])) {

        store_to_json($post_copy, $locations["logs_mail_disposable"], $is_receipt_asked);
        return false;

    }

    // Look out for honey-pot
    if (!isset($post_copy[0])) {

        unset($post_copy['first_input']); // Remove that useless key because it was not filled
        --$removed_keys;

    } else {

        // Store the mail in the rejected location, while not saying to the " user " it was rejected
        store_to_json($post_copy, $locations["logs_mail_rejected"], $is_receipt_asked);
        return false;

    }

    // Look if user has asked to receive a copy of the e-mail
    if (array_key_exists('receive_ack_receipt', $post_copy)) {

        unset($post_copy['receive_ack_receipt']); // This key is not needed anymore
        $is_receipt_asked = true; // Keeping info in $is_receipt_asked (receipt asked)
        --$removed_keys;

    } else {

        $is_receipt_asked = false; // None was asked

    }

    // Look if user has checked the (mandatory) checkbox for rules agreements
    if (!array_key_exists('data_sharing', $post_copy)) {

        store_to_json($post_copy, $locations["logs_mail_rejected"], $is_receipt_asked);
        return false; // Message will be rejected

    } else {

        unset($post_copy['data_sharing']); // Remove the key
        --$removed_keys;

    }

    // Test if the actual number of input is valid, all input minus the unset fields
    $post_copy_count = count($post_copy);
    $updated_count = count($post) - $removed_keys;

    if ((!$post_copy_count == $updated_count)) {

        store_to_json($post_copy, $locations["logs_mail_rejected"], $is_receipt_asked);
        return false; // Counting has gone wrong, message will be rejected

    }

    // Test the range for each input field as its pattern
    foreach ($post_copy as $key => $value) {

        // When the tested field is a subject prefix, there is no need to test it
        if ($key == "subject_prefix_list") {

            continue; // Skip the tests on this key (prefixed subject list)

        }

        /* Define the minimun and maximun allowed lenght (according to the actual field tested) as what pattern we need
         * to check the input field against. */
        $min = $html_elements_list[$key]['min_length'];
        $max = $html_elements_list[$key]['max_length'];
        $pattern = $html_elements_list[$key]['pattern_type'];

        // Store the current length value in character for actual count testing on the current input field
        $current_length = mb_strlen($value);

        // Test the range of the current field
        if ($current_length < $min || $current_length > $max) {

            store_to_json($post_copy, $locations["logs_mail_rejected"], $is_receipt_asked);
            return false; // Message will be rejected because lenght check returned an error on the current field

        }

        // Test the pattern of the current field
        if (!check_pattern($value, $pattern)) {

            store_to_json($post_copy, $locations["logs_mail_rejected"], $is_receipt_asked);
            return false; // Message will be rejected because pattern check returned an error


        }

    }

    // All is good
    store_to_json($post_copy, $locations["pending_mails"], $is_receipt_asked);
    return true;

}

/*
 * Redirect the client browser to the target (.html page) defined with $target argument.
 *
 * $target should be the full URL to the futur location:
 * scheme://domain-name/ressource_path
 *
 * Before actually sending the new location (redirection), checks if no headers was sent before.
 *
 * Return false if a header was already sent, otherwise silently exit once the client has been redirected.
 *
 */
function redirect_browser_to_new_location($target) {

    if (!headers_sent()) {

        // Redirect the client to the $target
        header('Location: ' . $target);

        return true; // Redirect the browser to $target

    } else {

        return false; // Does not redirect, an header has already been send

    }

}

/*
 * Reject all domains used in the form as 'e-mail' if it is listed as a non-trusty, disposable e-mail domains from such
 * service.
 *
 * $user_email is the value from the sender's email field used in the form, from $_POST request.
 *
 * If the mail domain is untrusty, report true (it's untrusty), otherwise false (it's trusty regarding the list).
 *
 * If the e-mail is misformated (for example, not 'at' ('@') character) return false as well.
 *
 */
function reject_disposable_email_domain($user_email) {

    // Load the list of non-trusty domains
    require 'var/untrusty_domains/disposable_email_domains.php';

    // Removes username from $user_email, that is not needed for the checking
    $matches = [];
    preg_match("/@(.+\..*$)/", $user_email, $matches);

    // Merge all sub-array into one
    $all_disposable_mails_domains = array_merge(...array_values($non_trusty_esp_domain));

    // Return a bool regarding if the matches is actually into the merged sub-arrays (the list of non-trusty domains)
    return (bool) (in_array($matches[1], $all_disposable_mails_domains));

}

/*
 * Once the mail has been given to PHPMailer, stores it to the good location, accordingly to PHPMailer returned code.
 *
 * ' rejected_mail ' goes into ' mail_dir/REJECTED ' subdirectory, and ' accepted_mail ' into ' mail_dir/ACCEPTED '.
 *
 * Move the mail from $pending_mail_path's directory to the subdirectory (see above).
 *
 * $pending_mail_path is the location where holded mails are waiting before being send.
 *
 * $status define where the mail should go (ACCEPTED/REJECTED).
 *
 * $locations is the array with all needed locations to retrieve and move the file.
 *
 * Return true or false.
 *
 */
function store_sended_mail_to_logs($pending_mail_path, $status, $locations) {

    // Define the prefix to use according to $status
    $prefix = (!$status) ? "rejected_mail" : "accepted_mail" ;

    // Extract current name of the file from whole path, matching everything after last slash ('/') (non-included)
    $file_current_name = substr($pending_mail_path, strrpos($pending_mail_path, '/' ) + 1);

    // Replace the file prefix according to $status
    $file_name = str_replace("mail_pending", $prefix, $file_current_name);

    // Define the full path for the file (so the name and its extensions are included)
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
 * $pending_mail_directory, the current directory where the pending mail file will be written (in JSON).
 *
 * $send_copy is a bool extracted from the original $_POST, which was unset while being copied into a variable.
 *
 * Returns nothing, write down the file.
 *
 */
function store_to_json($mail, $pending_mail_directory, $send_copy) {

    // Extra infos to adds to the file's name and it's content (statistic from user) at the end of the file's content
    $extra_info = [
        'IP'                => $_SERVER['REMOTE_ADDR'],
        'date_and_time'     => date('Y-m-d_His'),
        'send_copy'         => ($send_copy) ? true : false
    ];

    // Merging the $_POST from user, clean, with IP, date and time from request
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

    // Replace ' @ ' with ' _at_ ', this is not ok otherwise (would lead to weird naming for UNIX path)
    $file_name_without_at = str_replace("@", "_at_", $file_name);

    // Replace any space by an underscore (' _ '), for easier manipulation in shell (beside being legal and correct)
    $file_name_clean = preg_replace('/\s+/', '_', $file_name_without_at);

    // Prepare the name of the file and it's path
    $full_file_path = $pending_mail_directory . $file_name_clean; // Append the directory's path to the file name

    // Convert the raw content into JSON
    $json_content = json_encode($raw_content, JSON_PRETTY_PRINT);

    // Write down the file
    file_put_contents($full_file_path, $json_content, FILE_APPEND | LOCK_EX);

}

/*
 * From $input, check against $pattern_filter if the pattern is actually valid.
 *
 * Each input fields has to be defined into ' form_config.php ' file for this function to works.
 *
 * All arguments are mandatory.
 *
 * $input has to be a string, from the input field tested.
 *
 * $pattern_filter has to be extracted from ' $html_elements_list[$key]['pattern_type'] ' array, accordingly to each
 * field.
 *
 * Return true or false.
 *
 */
function check_pattern($input, $pattern_filter) {

    switch ($pattern_filter) {

        case "names":
            return (bool) preg_match("/^\p{Latin}+((?:-|\h)\p{Latin}+)*$/u", $input);

        case "email":
            return (bool) filter_var($input, FILTER_VALIDATE_EMAIL);

        case "text":
            return (bool) preg_match("/^[\p{Latin}\p{Common}\d\s\p{P}\p{S}]+$/u", $input);

        case "numbers":
            return (bool) filter_var($input, FILTER_VALIDATE_INT);
            //return (bool) preg_match("/^\d+$/g", $input);

        default: return false; // Not a valid filter

    }

}
