<?php

require_once 'functions.php';
require_once(__DIR__ . '/../config/settings.php');
require_once(__DIR__ . '/../config/form_config.php');
require_once(__DIR__ . '/../config/server_path_config.php');

// Set internal character encoding to UTF-8
mb_internal_encoding($char_encoding);

if (!checking_form($raw_post_copy, $html_elements_list, $locations)) {

    redirect_browser_to_new_location($rejected_sending_redirection); // Message has been rejected

} else {

    redirect_browser_to_new_location($accepted_sending_redirection); // Message has been accepted for futur sending

}

exit; // Should never reach that point
