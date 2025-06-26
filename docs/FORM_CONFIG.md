# form_config.php 

This file allow the administrator to define, from the actual mail form on the contact page, what each input are expected
from the user's request, how many of them, what are their respective length and pattern matching rules, and more.

This file is mandatory, you have to edit it to make Hermessenger works.

## Defining HTML elements in the contact form

Hermessenger is dumb, it needs to read what it would needs to checks.

When clicking on the submit button, all the datas are forwarded to ' src/checking_form.php ' from the main entry point, being ' public/main.php ' (inside the document root), which will operate a serie of test against the inputs.

It needs to know:

- How many fields they are to works on, by being written down into the $html_elements_list array.
- The allowed range (in case of input/textarea, an input field then).
- What is the type of pattern to test the data against.
- What are the ' id ' & ' name ' for each field, as it's ' html_tag '.

Each key of the ' $html_elements_list ' array from ' config/form_config.php ' being one of these.

If there these fields for example: 
- a firstname, 
- a secondname, 
- an email, 
- a subject, 
- a body, 
- a submit button 

that make 6, so 6 keys has to be defined.

If you need, for example, a checkbox to allow users to get a copy of their message too, it's 7. 

If you add any other input, textarea, checkbox, it's 8. And so on.

These 8 fields are mandatory and needs to be in the HTML source code as into this array.

Below an example for a contact form with:
- First name input
- Second name input
- Email input
- A zip code input
- A prefixed select list for subject
- A textarea for body
- A checkbox to ask to get a copy while sending the message
- A submit button to send the request.

```
$html_elements_list =
    [
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
                "min_length"   => 6,
                "max_length"   => 48,
                "id"           => "email",
                "name"         => "email",
                "pattern_type" => "email",
                "html_tag"     => "input",
                "placeholder"  => "jean.dupont@mail.com"
            ],

        "zipcode" =>
            [
                "min_length"   => 2,
                "max_length"   => 5,
                "id"           => "zipcode",
                "name"         => "zipcode",
                "pattern_type" => "numbers",
                "html_tag"     => "input",
                "placeholder"  => "75012"
            ],

        "subject_prefix" =>
            [
                "id"           => "subject_prefix",
                "name"         => "subject_prefix",
                "html_tag"     => "select"
            ],

        "body" =>
            [
                "min_length"   => 64,
                "max_length"   => 4096,
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

        "submit_button" =>
            [
                "id"            => "submit_button",
                "name"          => "submit_button",
                "html_tag"      => "button",
                "value"         => "Send your message"
            ]

];
```

Some above needs a bit of explaination:

- ' html_tag ' has to be the actual HTML tag used in the source code: input, textarea, checkbox, button, etc.

- ' pattern_type ' has to be in the following list of allowed value: names (for identity fields, such as firstname & secondname), text (for anything not being identity related), numbers (see zipcode) and email. Each of them having their own pattern matching rules, defined inside ' src/functions.php ' in the ' check_pattern() ' function. 

To remove a field, simply removes the whole key, for example for the same code without the subject_prefix, remove line from:

```
"subject_prefix" =>
```

To it's ending:

```
],
```

Which was, in this example, this whole piece of code:

```
        "subject_prefix" =>
            [
                "id"           => "subject_prefix",
                "name"         => "subject_prefix",
                "html_tag"     => "select"
            ],
```

To add a field, reverse this process and use the severals keys and value to define the property of this field.

Finally, to edit simply change the according value in the desired key with the according value.

Also, the orders of keys does not matter.
