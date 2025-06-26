<?php

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
#                                                                                             #
#                                      form_config.php                                        #
#                                                                                             #
# This is where you add, edit, removes HTML element on the contact form page of your website. #
#                                                                                             #
#       For more details about using this file, please read ' docs/FORM_CONFIG.md '.          #
#                                                                                             #
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

$html_elements_list =
    [
        "honeypot" =>
            [
                "min_length"   => 2,
                "max_length"   => 32,
                "id"           => "first_input",
                "name"         => "first_input",
                "pattern_type" => "names",
                "html_tag"     => "input",
                "placeholder"  => "Foo"
            ],

        "firstname" =>
            [
                "min_length"   => 2,
                "max_length"   => 32,
                "id"           => "firstname",
                "name"         => "firstname",
                "pattern_type" => "names",
                "html_tag"     => "input",
                "placeholder"  => "Jean",
            ],

        "secondname" =>
            [
                "min_length"   => 2,
                "max_length"   => 32,
                "id"           => "secondname",
                "name"         => "secondname",
                "pattern_type" => "names",
                "html_tag"     => "input",
                "placeholder"  => "Dupont",
            ],

        "email" =>
            [
                "min_length"   => 8,
                "max_length"   => 48,
                "id"           => "email",
                "name"         => "email",
                "pattern_type" => "email",
                "html_tag"     => "input",
                "placeholder"  => "jean.dupont@mail.com"
            ],

        "zipcode" =>
            [
                "min_length"   => 5,
                "max_length"   => 5,
                "id"           => "zipcode",
                "name"         => "zipcode",
                "pattern_type" => "numbers",
                "html_tag"     => "input",
                "placeholder"  => "75012"
            ],

        "subject_prefix" =>
            [
                "id"           => "subject_prefix_list",
                "name"         => "subject_prefix_list",
                "html_tag"     => "select"
            ],

/*        "subject" =>
            [
                "min_length"   => 8,
                "max_length"   => 48,
                "id"           => "subject",
                "name"         => "subject",
                "pattern_type" => "text",
                "html_tag"     => "input",
                "placeholder"  => "Subject of your e-mail",
            ],*/

        "body" =>
            [
                "min_length"   => 64,
                "max_length"   => 2048,
                "id"           => "body",
                "name"         => "body",
                "pattern_type" => "text",
                "html_tag"     => "textarea",
                "placeholder"  => "Your e-mail content"
            ],

        "ack_receipt_checkbox" =>
            [
                "id"            => "ack_receipt_checkbox",
                "name"          => "ack_receipt_checkbox",
                "html_tag"      => "checkbox",
                "label_message" => "Check this box if you want to receive a copy of your message"
            ],

        "data_sharing_checkbox" =>
            [
                "id"            => "data_sharing_checkbox",
                "name"          => "data_sharing",
                "html_tag"      => "checkbox",
                "label_message" => "Check this box to allow the sending of your e-mail"
            ],

        "submit_button" =>
            [
                "id"            => "submit_button",
                "name"          => "submit_button",
                "html_tag"      => "button",
                "value"         => "Send your message"
            ]

    ];
