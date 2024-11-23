# How to use it

## About the workflow

It is not very good as it is. It is expected to completly rewrite this for a more comfy, easy and more modern way. But for now it should works.

## How to install Hermessenger

This is recommanded for an actual usage of Hermessenger to use [Composer](https://getcomposer.org/) and install the package from [packagist.org](https://packagist.org/packages/barberousselibre/hermessenger).

## Define the SMTP info

- Copy ' vendor/barberousselibre/hermessenger/src/.env.example ' to ' vendor/barberousselibre/hermessenger/src/.env ':

```
cd /path/to/the/document_root_parent
cp vendor/barberousselibre/hermessenger/src/.env.example vendor/barberousselibre/hermessenger/src/.env
```

Then edit the file accordingly to your ESP's SMTP info, define as well your mailbox (being the recipient of the web mail form).

Once it's done, it is safe to remove ' vendor/barberousselibre/hermessenger/src/.env.example '.

## Configure the needed path

### Document root parent
- Copy ' vendor/barberousselibre/hermessenger/config/variables.example.php ' to ' vendor/barberousselibre/hermessenger/config/variables.php ':
```
cd /path/to/the/document_root_parent
cp vendor/barberousselibre/hermessenger/config/variables.example.php vendor/barberousselibre/hermessenger/config/variables.php
```

Then edit the file for these values only:
```
$document_root_parent = "";
``` 
*^ This path does not need an ending slash ('/')*

### Timezone
```
$timezone = "Europe/Paris";
```
*If another location is needed, please see [List of supported Timezone (from php.net)](https://www.php.net/manual/en/timezones.php)*

### Characters encoding
```
$char_encoding = "UTF-8";
```
*If another characters encoding is needed, please see [Supported characters encoding (from php.net)](https://www.php.net/manual/en/mbstring.supported-encodings.php)*

### Redirection after mail has been sended
```
$redirecting_location = ["scheme"             => "",
                         "domain_name"        => "",
                         "ressource_path"     => ""];
```

Where:
- scheme: should be ' http ' or ' https ', please do not add any thing else (as ' :// ').

- domain_name: should be the full URL, after scheme, without ' :// ', to the top-level-domain, such as ' www.example.org ', without ending slash ('/').

- ressource_path: should be the actual path to the redirecting page once the mail's request has been made (user clicked on sending mail button). Such as ' some_file.html '.

## Move the main.php into your document root

Last step being manually copy the ' vendor/barberousselibre/hermessenger/public/main.php ' file into your actual document root:
```
cd /path/to/the/document_root_parent
cp vendor/barberousselibre/hermessenger/public/main.php public/
```

Now once the send e-mail button will be hit by the user, main.php will do the entry point with the source code into ' vendor/* '.

## Permissions & ownership

WIP.

This is not a course about UNIX permissions and ownership.

These below are the notable files that needs tweaking for now:

- bin/send_mail_in_queue.php - Needs to allow execution for the user *or* the group executing this, which could be another user than the one running the web server, but in this case at least the group should be allowed to do so. As dhe directory ' bin/ ' should aswell get execution to allow to be opened by the user or groups.

- var/ - Needs to allow read & write for the user *or* group using this, being the web server no matter what. As the directory should allow read, write & execution for this to works (writting mail's file, moving and renaming them, etc).
