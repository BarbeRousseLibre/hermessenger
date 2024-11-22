# About files and directories

# public/ - The Document Root

Only files served by the web server, aka Document Root, are inside. The only exception being ' main.php ', being the entry point of Hermessenger.

- public/contact.example.html - example file, it is mostly used for testing and it could be replaced as well it with your own HTML code, probably a contact.html or what ever.

- public/main.php - the main entry point to PHP by the HTML form to ' src/checking_form.php '.

# bin/

Files that needs to be executed by other process than the web server, working in the background.

- bin/send_mail_in_queue.php - Once called, take from the ' var/temp_mail_directory ' the oldest mail, starting with the proper prefix (' mail_pending_ ') and send it, only this one, one-at-a-time. 

If a checkbox has been checked on the contact page holding the form, a second mail is sended as a receipt/copy for the user using the form.

# docs/

All documentations file that were " too much " to sets directly into [README.md](https://github.com/BarbeRousseLibre/hermessenger/blob/master/README.md).

Nobody likes wall of text.

# logs/

Where you could redirect output of some process, such as the crontask job calling ' bin/send_mail_in_queue.php ' or ' src/php_mailer.php ' as logs for Hermessenger.

# var/

Where the mails files are actually keep before and after sendings.

- var/temp_mail_directory - Holding mail until they are sended, being a pending queue.

- var/mail_dir/ - Hold sub-directories to store mails once they were passed through ' src/php_mailer.php ' (sending), regarding their status:

- var/mail_dir/ACCEPTED - Hold mails that was, regarding PHPMailer returned status, sended and accepted (but not necessary delivered).

- var/mail_dir/REJECTED - Hold mails that was, regarding a PHPMailer error code that was returned, rejected (no hope to see them delivered).

- var/mail_dir/UNTRUSTY/ - Holding sub-directories, regarding why the mail is untrusty:

- var/mail_dir/UNTRUSTY/DISPOSABLE - Mails request made from a disposable mailbox domain (such as Yopmail and other) are stored here and not sended.

# src/

Actual source code that does not need to be served by the web server.

- src/checking_form.php - take the $_POST from the user's input and test it against some condition (lenght, pattern matching, disposable e-mails domain list, etc), if all tests are succesful, then the data are exported to a JSON file into the ' var/temp_mail_directory ', until it is send by ' bin/send_mail_in_queue.php '.

- src/.env - The file used by PHPDotenv, allowing you to add sensitive informations and being sure they are safe (not accessible for client !) and properly stored.

- src/.env.example - File to renames ' src/.env ' for your usage. See ' src/.env ' above.

- src/functions.php - All functions used by Hermessenger, being built-in.

- src/php_mailer.php - The last file called by Hermessenger, sending the mail(s) once ' bin/send_mail_in_queue.php ' has been executed.

## src/var

Needed variables files used by the source code.

- src/var/untrusty_domains/disposable_email_domains.php - Listing all domains that is listed as a disposable e-mail address, rejecting them. This is safe to edit to removes or add new domains.

- src/var/mailing_var.php - This file rely on PHPDotenv, it reads from ' src/.env ' some sensitive datas: SMTP server, username, **password**, etc. You should write into ' src/.env ' the sensitive datas, nowhere else !

# config/

Holding configuration files, which has to be edited by the administrator.

- config/variables.php - This file is allowing you to setup your document root and the timezone, as the actual HTML tag's field used on your contact page.

- config/variables.example.php - Example file you have to modify, see ' src/var/variables.php ' right above.

# vendor/

Every composer packages that made Hermessenger works are installed here.

# Other

- .gitkeep - If you don't know what is it: A trick to force git to add empty directory that needs to be here to allow Hermessenger to works. If you do not commit anything to this project or will not forks it, this is safe to removes, but useless.

- composer.* - Files used by composer and interacting with ' vendor/ ' class's directory. 
