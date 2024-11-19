# hermessenger

Web mail form script for HTML contact page, written in PHP. 

Get e-mail request from a contact page, keeping it in a pending queue directory until it has to be send.

![Hermessenger logo, made by Blu](https://github.com/BarbeRousseLibre/hermessenger/blob/master/hermessenger_logo_320x320.jpg?raw=true)

# Dependencies

It uses [composer](https://github.com/composer/composer) to manage the following class:

- [PHPDotenv](https://github.com/vlucas/phpdotenv): allowing to properly protect some sensitives datas such as your ISP's SMTP server address, username, password, etc.*
- [PHPMailer](https://github.com/PHPMailer/PHPMailer): effectively and properly sends e-mail (IMAP).

\* *This doesn't seems like the most efficient way tho, it needs to be [cached](https://github.com/vlucas/phpdotenv/issues/207) and as it is, it's not.*

# Release - 0.1.2

This is a beta as it is for now (0.1.2). 

Some wanted and important features are still missing and will be added in futur releases.

I will say this once: **DO.NOT.USE.THIS.IN.A.PRODUCTION.ENVIRONMENT!**

It is **not** ready, simple as that. The features, directories and files hierarchy, workflow and… everything actually, will probably changes deeply in the next releases. 

This is a newbie project, keep that in mind.

**If you are looking for a strong and bullet-proof project, this is actually not the case.**

### Roadmap

Please see [ROADMAP.md](https://github.com/BarbeRousseLibre/hermessenger/blob/master/docs/ROADMAP.md).

## About & philosophy

This project was for me an exercice to learn PHP. It is written in PHP 8.2 and will soon be moved to 8.3 as well (0.2).

It was only tested on Linux with PHP 8.2 release for now.

### License
The source code I made is under [GPLv3 license](https://www.gnu.org/licenses/gpl-3.0.en.html). As the [logo](https://github.com/BarbeRousseLibre/hermessenger/blob/master/hermessenger_logo_320x320.jpg). 

For codes I did not made, please see the licenses of each project:

- [PHPDotenv license](https://github.com/vlucas/phpdotenv?tab=BSD-3-Clause-1-ov-file)
- [PHPMailer license](https://github.com/PHPMailer/PHPMailer?tab=LGPL-2.1-1-ov-file)

All of them are [free software](https://en.wikipedia.org/wiki/Free_software) (like in freedom, but also free beers).

### Goals

Please see [GOALS.md](https://github.com/BarbeRousseLibre/hermessenger/tree/master/docs/GOALS.md) for more details.

## Security

I try to do the things the right way, but I started this project from scratch with zero-PHP skills and almost no knowledge in this area (developping).

Be aware there is for now almost no tests against bots / AI, beside a pending mail queue and rejecting disposable e-mail domains, see below.

The usage of [PHPDotenv](https://github.com/vlucas/phpdotenv) isn't the expected way for production use for now, too.

## Availables features

1. Values from user's input are checked against PCRE2 rules (pattern matching) and for their lenght.

2. User could check a box in the form and receive at the same time an e-mail as a copy.

3. Copy the pending mail's file into a temporary directory until it is asked to send it. It allows massive attacks to be drasticaly slowed down and avoid to get a full mailbox or being blacklisted from your ISP's SMTP server. Please note these attacks are **only slowed down** and not blocked!

4. E-mail file, once it has been sended is moved and renamed into a sub-directory regarding it's status, ' var/mail_dir/ACCEPTED ' or ' var/mail_dir/REJECTED ', in JSON format.

5. Block request made using a list of disposable e-mails domains (non-exhaustive list). Please see ' [src/var/untrusty_domains/disposable_email_domains.php](https://github.com/BarbeRousseLibre/hermessenger/blob/master/src/var/untrusty_domains/disposable_email_domains.php) ', thus they are moved into ' var/mail_dir/UNTRUSTY/DISPOSABLE '.

6. Sensitives datas, which is your ISP's SMTP server info, are stored into ' src/.env ' to moves it from code as document root.

7. Mail file's name are using a formatted name from user's input data to quickly find out an e-mail regarding time and date, IP, firstname and second name as sender's e-mail address. As their current status : pending, accepted, rejected. See " How it works " below.

8. Mail's file's content store all data from the user input, in JSON, plus IP, date and time and if a copy was asked.

9. A hidden honey-pot field, named ' firstname_input ', allowing to checks if the request was made using a dumb-bot.

## Bugs (I am aware of)

1. When the function ' reject_disposable_email_domain() ' is called, return an error about an undefined array key:
> FastCGI sent in stderr: "PHP message: PHP Warning:  Undefined array key 1 in /path/to/the/document_root/hermessenger/src/functions.php on line 39" while reading response header from upstream, client: 192.168.1.44, server: sandbox.local, request: "POST /checking_form.php HTTP/1.1", upstream: "fastcgi://unix:/run/php-fpm/sandbox.local.sock:", host: "sandbox.local:8080", referrer: "http://sandbox.local:8080///index.html"

This is because this function is called before checking if the mailbox domains is listed as an untrusty domains, but not only ?

This is a very minor bug and don't block anything, it simply pollute logs for nothing as showing my workflow is not optimal.

Keeping it for 0.1.1 and will get fixed ASAP for a minor release between 0.1.1 and 0.2.

2. Bad usage of [PHPDotenv](https://github.com/vlucas/phpdotenv), see [this](https://github.com/vlucas/phpdotenv/issues/207).

## Missing features, things to do & work-in-progress

1. Adding a configuration system, avoiding administators to rely on editing .php files, such as TOML or JSON.

2. A captcha method that is not blocking some kind of users (disabled), such as peoples with dyslexia, to be able to prooves they are human and not a bot. This method should also be able to detect more modern bot or AI.

3. Allow to block Tor request, which could be used to hide the real client IP.

4. Allow to block VPN & request from proxy, which could be used to hide the real client IP.

5. Allow to block request made from a client's IP or mailbox that is known to be spamming (blacklist).

6. Parsing the content of subject and body to find out suspicious wording or link.

7. Uploading features with an anti-virus checks.

8. Adding support for non-Latin input data.

9. Testing this script on PHP8.3.

10. Testing this script on other operating system (such as *BSD or Windows).

11. Adding Robotframework + Selenium2Library tests suits, and how to make it works (and install pip, venv and such)…

12. Adding a feature to add a prefix in front of subject, regarding a list of subject into the mail form.

13. Internationalization system for a few languages : french, english, spanish… and more later.

14. Get a svg of the actual logo of Hermessenger, with a slight enhanced design.

15. An installation script, doing everything nicely in a few seconds, top.

## How to install it

**Last changes made me removes this section before the proper process has been defined.**

You can either download the source code from [github](git@github.com:BarbeRousseLibre/hermessenger.git) or [packagist.org](https://packagist.org/packages/barberousselibre/hermessenger) by using [Composer](https://getcomposer.org/).

### Security notes (for administators installing hermessenger):

1. Be aware that ' src/.env ' file should **NEVER** be accessible to your webserver (and so, client). This is why it is outside the document root.

2. As ' src/.env ' should stay inside ' .gitignore ' or any cvs system equivalent to avoid sharing it by mistake.

3. Be still sure to **never execute** this command:
```bash
$ git add src/.env
```

Only ' src/.env.example ' is safe for sharing (and you should **NEVER** use it directly).
If that so, changes ASAP your password, maybe user as well. This is your responsability as the one implementing my script. **Git never forget**!

4. NEVER use ' src/.env.example ', it as to belong to ' src/ ' as well (outside the document root).

5. It is also useless to manually block it from your webserver, because only ' /path/to/the/document_root/hermessenger/public/ ' should be accessible to your webserver, and should be your document root **no matter what**. WIP.

6. This also apply (1., 2., 3., 4., 5.), at a lesser degree of security issue, to ' src/var/variables.php ' as well. See ' src/var/variables.example.php '.

Soon some examples of configuration using NGinx & Apache2 will be provided, as tweaks for php.ini and a dedicated pool for it.

## How it works

Please see [HOW-IT-WORKS.md](https://github.com/BarbeRousseLibre/hermessenger/tree/master/docs/HOW-IT-WORKS.md) for more details.

## How to use it

You need a working mail service, or ESP, allowing you to use their SMTP servers to send the e-mail with PHPMailer.

Once you have these info, simply follow instruction into 'How to install it' it above.

Please see [HOW-TO-USE-IT.md](https://github.com/BarbeRousseLibre/hermessenger/tree/master/docs/HOW-TO-USE-IT.md) for more details.

### About some files

Please see [ABOUT-FILES.md](https://github.com/BarbeRousseLibre/hermessenger/tree/master/docs/ABOUT-FILES.md) for more details.

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
