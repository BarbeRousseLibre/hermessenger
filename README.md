# hermessenger
WIP - A self-made e-mail web form written in PHP, using composer, PHPDotenv &amp; PHPMailer. Be aware this is a newbie project, with (as is it for now) poor coding skills. Feel free to help me improves it. 

![Hermessenger logo, made by Blu](https://github.com/BarbeRousseLibre/hermessenger/blob/master/hermessenger_logo_320x320.jpg?raw=true)

## About

This project is simply a goal for me to learn PHP. It was written with PHP 8.2 and (for now) it was not tested on other release of PHP. This was also not tested on other operating system than Linux.

It is a free software, under GPL 3.0. Feel free to use it, enhance it…

This project reach a simple goal: Being a good, well-made, secure and easy-to-use and install system of mail form into a HTML page.

Until I say it's ready, it's not.

## Philosophy

This project is not made for companies or any web site with a high trafic. It is also not supposed to send more than 60 mails per hours, one per minute (which is already a lot). As it was not made with the idea to add this into CMS such as Wordpress or what ever, but for simple HTML page.

But, if you are looking for a simple mail form into a web page, for personnal use (commercial for small or one-man companies or not), for small project (personnal web site, etc) with small traffic, it could be good for your usage.

Hermessenger focus on sending a few mails per hours, for security reasons, nicely and slowly. Nothing more.

This does not also provide any fancy features as you could have with Javascript for example, and will never. It is made to be simple, quick to install and setup for administators. Feel free to add this layer your self or rely on other project.

This project is free (as free beer, but mostly as in « Freedom »), under GPLv3 license.

## This is a beta !

This project is not ready for production use, for now, considering using this for production environment is probably a very bad idea. This repository is public and is used to share my code to other peoples, helping me improving it.

## Security

I try to do the things the right way, but I started this project from scratch with zero-PHP skills and almost no knowledge in this area (developping). That is also one of the reasons to NOT USE this code in a production environment, for now.

I hope soon it will be good enough for this.

Also, be aware there is for now almost no tests against bots / AI / massive attack, beside a pending mail queue and rejecting disposable e-mail domains, see below.

## Availables features

- Checking if input's data from user's post ($_POST) are valid regarding their range (character's lenght).

- Checking if input's data from user's for each field is valid against PCRE2 pattern matching.

- Copy the mail's file into a pending queue until a crontask job, a loop or a manual call from PHP CGI is made.

- Send an e-mail to the recipient from the mail form.

- Allow the sender to get a copy of the mail sended to the sender's mailbox, by checking a checkbox right before sending the mail.

- Mail sended, using PHPMailer, are copied regarding the returned status, to the proper sub-directory.

- The sub-directory ' temp_mail_directory ' allows to slow down a "massive attack" from bots or peoples trying to send way too much mails. This is not intended to block any mails, simply blocking the possibility to quickly overload the mailbox, making it unavailable (no space left) as protecting the mail form from being blocked at the ESP side.

- Remove request made with a non-trusty and disposable e-mail domains (such as yopmail, but not only) and store them into 'mail_dir/UNTRUSTY/DISPOSABLE'.

- Safe and secure .env file, allowing administators to safely write their sensitive data (their ESP's SMTP server info, user, password, etc) without being worried to accidentaly giving them to a "client".

- Logged mail are named with most informations of the mail: status (pending, accepted, rejected), date & time, IP of the client, first and second name and e-mail address.

- Logged mail are having all datas from the request, plus a bool (true|false) showing if a copy/receipt was asked from the client.

- Honey-pot trap.

## Missing features before first release

- Adding TOML support and removes from src/var/variables.php anything that could lead administators to break code by accident, making Hermessenger easier to setup and tweak around.

- A nicer way to exit if something goes wrong, specially for the honey-pot feature: Hermessenger should return as a response the same as it would for a legitimate user to fool it even more.

About: When achieve, Hermessenger will be considered secure, working and tested for most scenario. It will be considered by me as ready for production use.

## Things to do before first release

- Complete "missing features before first release"

- Parsing code to harmonize style


## Missing features, things to do & work-in-progress

- Captcha / «Are you a human?» / Question, that are efficient but at the same time does not rely on third-party services (such as Google) and does not collect any datas.

- Allow to block Tor request, which could be used to hide the real client IP.

- Allow to block VPN & request from proxy, which could be used to hide the real client IP.

- Adding a list of non-trusty mailbox and IP.

- Comparing time between the client receiving the mail's form request and the mail's sending, checking for too quickly filled form.

- Checking the content of body & subject to found out suspicious wording, link, etc.

- An uploading features for files, with anti-virus checking, size blocking, mime content type blocking, etc.

- Adding support for non-Latin input data.

- Testing this script on PHP8.3.

- Testing this script on other operating system (such as *BSD or Windows).

- Adding Robotframework + Selenium2Library tests suits, and how to make it works (and install pip, venv and such)…

- Adding a feature to add a prefix in front of subject, regarding a list of subject into the mail form.

- Adding a sweet way to add a prefix for subject as body, allowing to know easier and quicker that the current mail of the recipient's mail box is coming from this script.

- Giving the choice for pre-defined natural languages, probably these one: french, english, spanish. Some more could be added, later, like Chinese, Arabic, Hindi, Russian, Ukrainian and other cyrillic languages, and more. This first needs a better implementation of the pattern matching actually used, which only allow Latin-alike alphabet. Please note, this won't use translation software or AI translation. If you want to help, let's do it properly :) !

- Get a svg of the actual logo of Hermessenger.

## How it works

From input's user into a HTML mail form to send a message, check validity of the input and write a file into this format :

> |0|firstname|Foo|

> |1|secondname|Bar|

> |2|email|foo.bar@gogole.com|

> |3|subject|This is a simple test for a message|

> |4|body|Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit  in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum|

> |5|date_and_time|YYYY-MM-DD_hhmmss|

> |6|IP|XXX.XXX.XX.XXX|

> |7|send_copy|bool|

The mail will take this type of name :

> pending_mail_YYYY-MM-DD_hhmmss_XXX.XXX.XX.XXX_Foo_Bar_foo.bar_at_gogole.com.txt

This file will be stored into ' mail_temp_directory '.

Per default, this is not sending any mail at this point. This was written to be used aside a task schedular (such as cron), a loop or you can (for testing purpose) execute the following file manually on a shell : ' send_mail_in_queue.php '

The goal is to make impossible to overload your mailbox, or being blocked by your ESP's SMTP server for abusing it without knowing it. My advice is to look up what's your sending limits and imagine the following scenario : If someone manage to abuse this form, adding 10k mails into your mail pending queue, that won't block you in any way : If you set a call to ' send_mail_in_queue.php ' every 5 minutes, it means you won't send more than 60 ÷ 5 = 12 mails per hours, 12 × 24 = 288 mails per day. **Warning : This has to be doubled if you wants to allow your user to get a receipt / copy on their own mailbox, making it to 576 e-mails sends / day.**

This script will look into ' temp_mail_directory ' and report the oldest mail file. Once one has been found, invoke ' php_mailer.php ' which will try to actually send the mail. Regarding the code returned by PHPMailer, will move the mail files into ' mail_dir/ACCEPTED ' or ' mail_dir/REJECTED '. It's important to note : If PHPMailer return true, it does NOT MEANS YOUR MAIL IS ACTUALLY SENDED TO THE MAILBOX. The recipient server could reject it for many reasons. 

The mail file is also renamed regarding it's status (returned by PHPMailer), 'accepted_mail…' or 'rejected_mail…'.

This allows you to keep a trace of every message, even if they are removed from the mailbox, as logs and helping you getting statistic from file's name and it's content, and finally detect non-sended message (from PHPMailer side only, so your own side).

## How to install it

1. Download the code into your Document Root and configure your web server's virtual host (the site you want to add this form) regarding your need. See NGinX example below.

2. Renames src/var/variables.example.php to src/var/variables.php. Once it's done, open it and edit values accordingly to your needs (at least $document_root and probably $timezone):

```bash
cd /path/to/document_root/hermessenger/ && mv src/var/variables.example.php src/var/variables.php && echo -n "Success !\n"
```

3. Edit .env.example file accordingly to your ESP's SMTP parameters. You need to edit without adding at the end of the line the semicolon (';'):

```
SMTP_USER = "sender_username"
SMTP_DOMAIN = "@domain.org"
SMTP_PASSWORD = "super_secret_password"
SMTP_SERVER = "mail.domain.org"
SMTP_PORT = yourport

RECIPIENT_USER = "recipient_username"
RECIPIENT_DOMAIN = "@domain.org"
```

To:

```
SMTP_USER = "contact"
SMTP_DOMAIN = "@mywebsite.com"
SMTP_PASSWORD = "1234_abcdefg!"
SMTP_SERVER = "mail.myesp.org"
SMTP_PORT = 465

RECIPIENT_USER = "recipient_username"
RECIPIENT_DOMAIN = "@domain.org"
```

4. Once you are sure of your parameters, renames it to .env:

```bash
cd /path/to/document_root/hermessenger/ && mv src/.env.example src/.env && echo -n "Success !\n"
```

### Security notes (for administators installing hermessenger):

1. Be aware that .env file should NEVER be accessible to your webserver (and so, client).

2. As it should always be added to .gitignore or any cvs system equivalent.

3. Be still sure to never 'git add src/.env', only .env.example is safe. If that so, changes ASAP your password, maybe user as well. This is your responsability as the one implementing my script. Git never forget!

4. NEVER use .env.example, and do not renames or moves it. It as to belong to src/ as well.

5. It is also useless to manually block it from your webserver, because only public/ should be accessible to your webserver, and should be your document root **no matter what**.

6. This also apply, at a lesser degree of security issue, to src/var/variables.php as well. See src/var/variables.example.php.

Soon some example of configuration using NGinx & Apache2 will be provided, as tweaks for php.ini and a dedicated pool for it.

The user running the cron task job needs to be able to access the PHP's binary as the Document Root holding it.


## How to use it

You need a working mail service, or ESP, allowing you to use their SMTP servers to send the e-mail with PHPMailer.

Once you have these info, simply follow instruction into 'How to install it' it above.

### About some files
- public/index.html - mandatory file, it was mostly used by me for my testing and you could replace as well it with your own HTML code.
- public/checking_form.php - take the $_POST from the user's input and test it against some condition (lenght, pattern matching, etc), if all tests are succesful, then the data are exported to a plaintext file into the " temp_mail_directory ", until it is send by " send_mail_in_queue.php ".

- src/send_mail_in_queue.php - Once invoked, take from the " temp_mail_directory " the oldest mail and send it, only this one. If a checkbox has be checked on the index.html page, a second mail is sended as a receipt/copy for the user using the form.
- src/var/mailing_var.php - This file rely on PHPDotenv, it reads from src/.env some sensitive datas: SMTP server, username, **password**, etc. You should write into .env the sensitive datas, nowhere else !
- src/var/variables.php - This file is allowing you to add or remove fields, there is variables you could modify to adapt the code to your actual HTML page and need.
- src/var/variables.example.php - See How to use it above.
- src/.env - The file used by PHPDotenv, allowing you to add sensitive informations and being sure they are safe (not accessible for client !) and properly stored.
- src/.env.example - File to renames .env for your usage. See .env above.
- src/var/untrusty_domains/disposable_email_domains.php - Listing all domains that is listed as a disposable e-mail address, rejecting them.

- .gitkeep - File allowing me to send "empty" directory to git, this is safe to removes them, unless you need to push code to this repository.

## Special thanks to… and credit

- Everyone's helping me by answering my answers or showing what was bad, you are great !

- [PHP](https://www.php.net) for their documentation.

- [KDEvelop](https://kdevelop.org/) for being a neat IDE.

- [PHPMailer](https://github.com/PHPMailer/PHPMailer) for the ease to install and the good work on sending mails.

- [PHPDotenv from vlucas](https://github.com/vlucas/phpdotenv) for making this works more secure, easily.

- [Justine MULLER](https://justine-muller.fr) for her advice and guidance on HTML.

- [Blu](https://www.instagram.com/bluareus/) for her cute logo she made pretty quickly !

## Last words

I can not push this more, but… **THIS IS NOT READY FOR PRODUCTION USE**. If you do it, well… 

Feel free to help me improves it, I'm open to critics (if they are constructive).

Just keep in mind : There is obviously better project around, doing this, with better or more modern way. This is one of my goal : Improves it to reach the " perfection ".

Thanks, stay safe.

GASPARD DE RENEFORT Kévin
