# How to use it

## About the workflow

It is not very good as it is. It is expected to completly rewrite this for a more comfy, easy and more modern way. But for now it should works.

__TL;DR:__
- 1. Install Hermessenger and the needed dependancies by using [Composer](https://getcomposer.org/) or from source code on git directly.
- 2. Copy .env.example file to .env, located in ' vendor/barberousselibre/hermessenger/src/ '.
- 3. Edit the .env file accordingly to your SMTP account.
- 4. Copy settings.example.php to settings.php, located in ' vendor/barberousselibre/hermessenger/config/ '.
- 5. Edit settings.php file accordingly to your own need, mandatory variables being $char_encoding, $timezone.
- 6. Edit variables.php file accordingly to your document root location, adding the **parent** directory (one level above) is __important__. See $document_root_parent.
- 7. Finally for settings.php, fill the array $redir_location[] with your desired schemes (http or https), the domain name and the target file used for redirection once the user has clicked on send the mail.
- 8. Copy ' vendor/barberousselibre/hermessenger/public/main.php ' to your actual document root.
- 9. Set the proper ownership & permissions for ' vendor/barberousselibre/hermessenger/bin/send_mail_in_queue.php ' (the user executing this should get 7, or rwx, on the file and it's parent directory).
- 10. Set the proper ownership & permissions for ' vendor/barberousselibre/hermessenger/var ' directory, allowing web server user and / or group reading, writing and execution.

## How to install Hermessenger

This is recommanded for an actual usage of Hermessenger to use [Composer](https://getcomposer.org/) and install the package from [packagist.org](https://packagist.org/packages/barberousselibre/hermessenger). From the github's repo it should works too, but with more tweaking.

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
- Copy ' vendor/barberousselibre/hermessenger/config/settings.example.php ' to ' vendor/barberousselibre/hermessenger/config/settings.php ':
```
cd /path/to/the/document_root_parent
cp vendor/barberousselibre/hermessenger/config/settings.example.php vendor/barberousselibre/hermessenger/config/settings.php
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
$redir_location = ["scheme"             => "",
                   "domain_name"        => "",
                   "ressource_path"     => ""];
```

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
