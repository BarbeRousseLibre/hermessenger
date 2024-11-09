# hermessenger

Home-made web mail form for HTML contact page, written in PHP. It uses composer to manage the following class:

- PHPDotenv, allowing to properly protect some sensitives datas such as your ISP's SMTP server address, username, password, etc.
- PHPMailer, to effectively and properly sends e-mail (IMAP).

This is a beta as it is for now. Some wanted features are missing, see below.

![Hermessenger logo, made by Blu](https://github.com/BarbeRousseLibre/hermessenger/blob/master/hermessenger_logo_320x320.jpg?raw=true)

## About

This project was for me an exercice to learn PHP. It's written in PHP 8.2 and will soon be moved to 8.3 as well.

It was only tested on Linux with PHP 8.2 release.

This project is using GPLv3 license. As the logo.

This is the 0.1 release and is still in beta, **you should probably avoid it in production**.

## Philosophy

Hermessenger aim to sends a few mails by hours, keeping futur mail's sending into a pending queue directory until a loop, a crontask job or even a PHP-CGI is call on ' src/send_mail_in_queue.php ' script.

This is not made to support a lot of sending in a short period of time, instead it's expected to hold mails for a few minutes to manage potential threat against your recipient's mailbox.

It aims simplicity, ease of installation and configuration. Be aware, for now this is still under work and adding or removing one of the expected fields (firstname, secondname, email (of user), subject and body) would result in unexpected behavior.

Please, if you are looking for a strong and bullet-proof project, this is actually not the case.

## This is a beta !

This project is not ready for production use, for now, considering using this for production environment is probably a very bad idea. This repository is public and is used to share my code to other peoples, helping me improving it.

**Actual release is 0.1.**

Futur release (0.2) will allow:

- Add, removes or modifier in a simple maner the HTML fields expected.
- Rely on a configuration file that would greatly ease the settings of Hermessenger.

## Security

I try to do the things the right way, but I started this project from scratch with zero-PHP skills and almost no knowledge in this area (developping). That is also one of the reasons to NOT USE this code in a production environment, for now.

I hope soon it will be good enough for this.

Also, be aware there is for now almost no tests against bots / AI / massive attack, beside a pending mail queue and rejecting disposable e-mail domains, see below.

## Availables features

1. Tests values from the user's input request, for range and allowed pattern.

2. User could check a box and receive at the same time an e-mail as a copy.

3. Copy the pending mail's file into a temporary directory until it is asked to send it. It allows massive attacks to be drasticaly slowed down and avoid to get a full mailbox or being blacklisted from your ISP's SMTP server. Please note these attacks are **only slowed down** and not blocked.

4. E-mail file, once it has been tried to send it, is moved and renamed into a sub-directory regarding it's status, ' mail_dir/ACCEPTED ' or ' mail_dir/REJECTED '.

5. Block request made using a list of disposable e-mails domains (non-exhaustive list). Please see ' [src/var/untrusty_domains/disposable_email_domains.php](https://github.com/BarbeRousseLibre/hermessenger/blob/master/src/var/untrusty_domains/disposable_email_domains.php) ', thus they are moved into ' mail_dir/UNTRUSTY/DISPOSABLE '.

6. Sensitives datas, which is your ISP's SMTP server info, are nicely stored into ' src/.env ' to moves it from code, document root as well.

7. Mail file's name are using a formatted name from user's input data to quickly find out an e-mail regarding time and date, IP, firstname and second name as sender's e-mail address. As their current status : pending, accepted, rejected. See "How it works" below.

8. Mail's file's content store all data from the user input, plus IP, date and time and if a copy was asked.

9. A hidden honey-pot field, named 'firstname_input', allowing to checks if the request was made using a dumb-bot.

## Missing features, things to do & work-in-progress

1. Adding a configuration system, avoiding administators to rely on editing .php files, such as TOML.

2. A captcha method that is not blocking some kind of users, such as peoples with dyslexia, to be able to prouves they are human and not a bot. This method should also be able to detect more modern bot or AI.

3. Allow to block Tor request, which could be used to hide the real client IP.

4. Allow to block VPN & request from proxy, which could be used to hide the real client IP.

5. Allow to block request made from a client's IP that is known to be spamming (blacklist).

6. Check the client's time between the request of the contact page holding the form and the time the user send the request. If it's too fast, then it's suspicious and could be a bot / AI.

7. Parsing the content of subject and body to find out suspicious wording.

8. Uploading features with an anti-virus checks.

9. Adding support for non-Latin input data.

10. Testing this script on PHP8.3.

11. Testing this script on other operating system (such as *BSD or Windows).

12. Adding Robotframework + Selenium2Library tests suits, and how to make it works (and install pip, venv and such)…

13. Adding a feature to add a prefix in front of subject, regarding a list of subject into the mail form.

14. Internationalization system for a few languages : french, english, spanish… and more later.

16. Get a svg of the actual logo of Hermessenger, with a slight enhanced design.

## How it works

**Please note**: This is not, for now, a perfect and easy methods, it lacks features. This is still a work-in-progress.

1. Check validity of the user input and write a file into this format :

> |0|firstname|Foo|

> |1|secondname|Bar|

> |2|email|foo.bar@gogole.com|

> |3|subject|This is a simple test for a message|

> |4|body|Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit  in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum|

> |5|date_and_time|YYYY-MM-DD_hhmmss|

> |6|IP|XXX.XXX.XX.XXX|

> |7|send_copy|bool|

Each row could be represented this way :
```|line number|html tag|user input```
Where | (pipe) are used as delimiter.

The mail will take this type of name :

> status_mail_YYYY-MM-DD_hhmmss_XXX.XXX.XX.XXX_firstname_secondname_recipient-mailbox_at_domain.tld.txt

Where:
- **status_mail**: pending, accepted, rejected. The actual mail's status regarding the current workflow.
- **YYYY-MM-DD**: date in the international format.
- **hhmmss**: time, 24h format, with seconds.
- **XXX.XXX.XX.XXX**: the IP sending the request, which is **not a trusty** value.
- **firstname**: the value inside the user's input. An underscore ('_') is replacing the following white space.
- **secondname**: the value inside the user's input.
- **recipient-mailbox**: the name of the mailbox in the user's input.
- **at**: the '@' (at) is replaced by '\_at\_' to avoid error in Unix's filesystem.
- **domain.tld**: the ISP domain holding 'recipient-mailbox' user.
- **.txt**: The content mime type of the file.

And:
- **_**: Field delimiter between each value, replace also white-space characters when needed.

This file will be stored into ' mail_temp_directory/ '.

Per default, this is not sending any mail at this point. This was written to be used aside a task schedular (such as a cronjob), a loop or you can (for testing purpose) execute the following file manually on a shell : ' send_mail_in_queue.php ':

### Crontask job:

First be sure to target the crontask file from an user with the permission to use ' /usr/bin/phpX.X ' binary.

Then, this user should have the read and execution permissions on the document root as the ' src/send_mail_in_queue.php ' script.

Before actually settings this into your user's crontab, try executing it manually, see point ' PHP-CGI manual sending / testing '. below.

The output could be redirected to some logs file, per default you could use ' logs/ ' directory. So the user should as well be able to read, write and execute into this directory.

**Note:** If into ' src/php_mailer.php ' you turned on the debug features, expect dozens of line for each sending, instead of one, as this example showing when debug features is off:

```Message has been sent the 2024-11-09, at 11:21:14.```

Open your user's crontab the good way (since you are going in hell if you edit this manually):
```bash
$ crontab -u youruser -e
```

Then add (for a sending every 5 minutes):

```
*/5 * * * * /usr/bin/php8.2 /path/to/the/document_root/hermessenger/src/send_mail_in_queue.php >> /var/www/localhost/htdocs/hermessenger/logs/sending_logs.txt
```

*Do not forget to save and quit, otherwise this won't be applied until so !*

### PHP-CGI manual sending / testing:

Simply execute the following command:
```bash
$ /usr/bin/php8.2 /path/to/the/document_root/hermessenger/src/send_mail_in_queue.php >> /var/www/localhost/htdocs/hermessenger/logs/sending_logs.txt
```

To be sure to see it works when using crontab (see above, ' Crontask job '), hit the full path for your PHP binary and proper release if needed.

### Bash loop using while (300 seconds, so 5 minutes):
```bash
$ while true; do /usr/bin/php8.2 /path/to/the/document_root/hermessenger/src/send_mail_in_queue.php; sleep 300; done
```

And stop it using:

```bash
$ ctrl^c
```

Or to pause it for a while:

```bash
$ ctrl^z
```

And to unpause it:

```bash
$ fg
```

This is nice for testing without actually modifying the crontab. You could also runs this from a screen, but I won't recommands it since cron is doing it much more nicely.

2. The goal is to make impossible to overload your mailbox, or being blocked by your ESP's SMTP server for abusing it without knowing it (sending limit ration). To setup the sending rate from Hermessenger, you should look up what's your sending limits and imagine the following scenario:

If someone manage to abuse this form, adding 10k mails into your mail pending queue, that won't block you in any way. If you set a call to ' send_mail_in_queue.php ' every 5 minutes, it means you won't send more than 60 ÷ 5 = 12 mails per hours, 12 × 24 = 288 mails per day.
**Warning : This has to be doubled if you wants to allow your user to get a receipt / copy on their own mailbox, making it to 576 e-mails sends / day.**

This is your job to define what's the maximum limit for the mail's sending per hour. You should also keep in mind that leaving a safe difference between what could be sended per day by Hermessenger and what's your actual limit ration is important: This domains, probably sharing among different mailbox, this ratio should still be able to sends mails.

3. Once ' src/send_mail_in_queue.php ' is called, it will look up into ' temp_mail_directory ' and report the oldest mail file (if any). Once one has been found, invoke ' src/php_mailer.php ' which will try to actually send the mail. It will ignore '.', '..' (Unix path) as '.gitkeep'.

4. Regarding the code returned by PHPMailer, will move the mail files into ' mail_dir/ACCEPTED ' or ' mail_dir/REJECTED '.

**It's important to note : If PHPMailer return true, it does NOT MEANS YOUR MAIL IS ACTUALLY SENDED TO THE MAILBOX. The recipient server could reject it for many reasons.**

To find out the reason, enable debug's feature inside ' src/php_mailer.php '.

The mail file is also renamed regarding it's status (returned by PHPMailer), 'accepted_mail…' or 'rejected_mail…' in the expected directory.

This allows you to keep a trace of every message, even if they are removed from the mailbox, as logs and helping you getting statistic from file's name and it's content, and finally detect non-sended message (from PHPMailer side only, so your own side).

## How to install it

**WIP: As it is for now, this needs to follow these steps to be working, a better way will be added in futur realeses.**

You need, once the code will be copied on your server, to moves all your files (.html, .css, .js, and all others assets) into ' public/ ', as the script expect to have all the needed file into ' public/ '.

1. Download the code into your Document Root:

```bash
$ cd /path/to/document_root
$ git clone git@github.com:BarbeRousseLibre/hermessenger.git
```

And moves all your website's file into ' public/ ', as all assets directory needed to your website.

2. Renames ' src/var/variables.example.php ' to ' src/var/variables.php '. Once it's done, open it and edit values accordingly to your needs (at least ' $document_root ' and probably ' $timezone '):

```bash
$ cd /path/to/document_root/hermessenger/
$ mv src/var/variables.example.php src/var/variables.php && echo -n "Success !\n"
```

3. Renames ' src/.env.example ' to ' src/.env ', and edit it this file accordingly to your ESP's SMTP parameters. You need to edit without adding at the end of the line the semicolon (';'):

```bash
$ cd /path/to/document_root/hermessenger/
$ mv src/.env.example src/.env && echo -n "Success !\n"
```

5. Install composer, following these instructions on composer's website (https://getcomposer.org/download/).

Go into the document root and execute:

```bash
$ cd /path/to/document_root/hermessenger/
$ php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
$ php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
$ php composer-setup.php
$ php -r "unlink('composer-setup.php');"
```
**Be aware: these instructions right above has to be updated regarding what's asked on getcomposer.org/download. This example above could be outdated.**

Once it's done, you should get a ' src/vendor/ ' directory and the needed class should works flawlessly.

### Security notes (for administators installing hermessenger):

1. Be aware that ' src/.env ' file should **NEVER** be accessible to your webserver (and so, client). This is why it is outside the document root.

2. As ' src/.env ' should stay inside .gitignore or any cvs system equivalent to avoid sharing it by mistake.

3. Be still sure to never execute this command:
```bash
$ git add src/.env
```

Only .env.example is safe (and you should **NEVER** use it directly).
If that so, changes ASAP your password, maybe user as well. This is your responsability as the one implementing my script. **Git never forget**!

4. NEVER use ' src/.env.example ', it as to belong to ' src/ ' as well (outside the document root).

5. It is also useless to manually block it from your webserver, because only ' public/ ' should be accessible to your webserver, and should be your document root **no matter what**.

6. This also apply (1., 2., 3., 4., 5.), at a lesser degree of security issue, to ' src/var/variables.php ' as well. See src/var/variables.example.php.

Soon some example of configuration using NGinx & Apache2 will be provided, as tweaks for php.ini and a dedicated pool for it.

## How to use it

You need a working mail service, or ESP, allowing you to use their SMTP servers to send the e-mail with PHPMailer.

Once you have these info, simply follow instruction into 'How to install it' it above.

### About some files
- public/index.html - mandatory file, it was mostly used by me for my testing and you could replace as well it with your own HTML code.
- public/checking_form.php - take the $_POST from the user's input and test it against some condition (lenght, pattern matching, etc), if all tests are succesful, then the data are exported to a plaintext file into the ' public/temp_mail_directory ', until it is send by ' src/send_mail_in_queue.php '.

- src/send_mail_in_queue.php - Once invoked, take from the ' temp_mail_directory ' the oldest mail and send it, only this one. If a checkbox has be checked on the contact page holding the form, a second mail is sended as a receipt/copy for the user using the form.
- src/var/mailing_var.php - This file rely on PHPDotenv, it reads from ' src/.env ' some sensitive datas: SMTP server, username, **password**, etc. You should write into ' src/.env ' the sensitive datas, nowhere else !
- src/var/variables.php - This file is allowing you to setup your document root and the timezone, as the actual HTML tag's field used on your contact page.
- src/var/variables.example.php - Example file you have to modify, see ' src/var/variables.php ' right above.
- src/.env - The file used by PHPDotenv, allowing you to add sensitive informations and being sure they are safe (not accessible for client !) and properly stored.
- src/.env.example - File to renames ' src/.env ' for your usage. See ' src/.env ' above.
- src/var/untrusty_domains/disposable_email_domains.php - Listing all domains that is listed as a disposable e-mail address, rejecting them. This is safe to edit to removes or add new domains.

- .gitkeep - If you don't know what is it: A trick to force git to add empty directory that needs to be here to allow Hermessenger to works. If you do not commit anything to this project or will not forks it, this is safe to removes, but useless.

## Special thanks to… and credit

- Everyone's helping me by answering my answers or showing what was bad, you are great ! Special thanks to #php@irc.libera.chat and « Les joies du code » Discord server for their advice and patience.

- [PHP](https://www.php.net) for their documentation.

- [KDEvelop](https://kdevelop.org/) for being a neat IDE.

- [PHPMailer](https://github.com/PHPMailer/PHPMailer) for the ease to install and the good work on sending mails.

- [PHPDotenv from vlucas](https://github.com/vlucas/phpdotenv) for making this works more secure, easily.

- [Justine MULLER](https://justine-muller.fr) for her advice and guidance on HTML.

- [Blu](https://www.instagram.com/bluareus/) for her cute logo she made pretty quickly !

- [Les esprits atypiques](https://les-esprits-atypiques.org) for giving me a good case to learn PHP.

## Last words

I can not push this more, but… **THIS IS NOT READY FOR PRODUCTION USE**. If you do it, well… 

Feel free to help me improves it, I'm open to critics (if they are constructive).

Just keep in mind : There is obviously better project around, doing this, with better or more modern way. This is one of my goal : Improves it to reach the " perfection ".

Thanks, stay safe.

GASPARD DE RENEFORT Kévin
