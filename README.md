# hermessenger

Hermessenger is a sending e-mail form for HTML web page, written in PHP, aiming simplicity and security.

This is a Work-In-Progress project, not ready for actual production use. It was made for learning PHP basics on my side.

Do not expect this code to be ready for a production environment, please. Its usage is for now heavily suspsicious and under tests.

![Hermessenger logo, made by Blu](https://github.com/BarbeRousseLibre/hermessenger/blob/master/hermessenger_logo_320x320.jpg?raw=true)

# Dependencies

It uses [composer](https://github.com/composer/composer) to manage the following class:

- [PHPDotenv](https://github.com/vlucas/phpdotenv): allowing to properly protect some sensitives datas such as your ISP's SMTP server address, username, password, etc.*
- [PHPMailer](https://github.com/PHPMailer/PHPMailer): effectively and properly sends e-mail (SMTP).

\* *This doesn't seems like the most efficient way tho, it needs to be [cached](https://github.com/vlucas/phpdotenv/issues/207) and as it is, it's not.*

### Warning before installing and using this code

Some wanted and important features are still missing and will be added in futur releases.

I will say this once again and a last time: **DO.NOT.USE.THIS.IN.A.PRODUCTION.ENVIRONMENT!**

It is **not** ready, simple as that. The features, directories and files hierarchy, workflow and… everything actually, will probably changes deeply in the next releases. 

This is a newbie project, keep that in mind, please.

**If you are looking for a strong and bullet-proof project, this is actually not the case as it is for now.**

Feel free to ask me anything regarding this project.

### Roadmap

Please see [ROADMAP.md](https://github.com/BarbeRousseLibre/hermessenger/blob/master/docs/ROADMAP.md).

## About & philosophy

This project was for me an exercice to learn PHP. It is written to works with the last stable PHP release (for now PHP8.3).

Works with PHP8.2 and PHP8.3.

### License

The source code is under [GPLv3 license](https://www.gnu.org/licenses/gpl-3.0.en.html). As the [logo](https://github.com/BarbeRousseLibre/hermessenger/blob/master/hermessenger_logo_320x320.jpg). 

For codes I did not write, please see the licenses of each project:

- [PHPDotenv license](https://github.com/vlucas/phpdotenv?tab=BSD-3-Clause-1-ov-file)
- [PHPMailer license](https://github.com/PHPMailer/PHPMailer?tab=LGPL-2.1-1-ov-file)

All of them are [free software](https://en.wikipedia.org/wiki/Free_software) (like in freedom, but also free coffee).

### Goals

Please see [GOALS.md](https://github.com/BarbeRousseLibre/hermessenger/tree/master/docs/GOALS.md) for more details.

## Security

I try to do the things the right way, but I started this project from scratch with zero-PHP skills and almost no knowledge in this area (developping).

Be aware there is for now almost no tests against bots / AI, beside a pending mail queue and rejecting disposable e-mail domains and an honey-pot, see below.

The usage of [PHPDotenv](https://github.com/vlucas/phpdotenv) isn't the expected way for production use for now, too.

Important features are coming soon, specially against spams and bots.

## Availables features

1. Values from user's input are checked against PCRE2 rules (pattern matching) and for their lenght.
2. User could check a box in the form and receive at the same time an e-mail as a copy.

3. Copy the pending mail's file into a temporary directory until it is asked to send it. It allows massive attacks to be drasticaly slowed down and avoid to get a full mailbox or being blacklisted from your ISP's SMTP server. Please note these attacks are **only slowed down** and not blocked!

4. E-mail file, once it has been sended is moved and renamed into a sub-directory regarding it's status, ' var/mail_dir/ACCEPTED ' or ' var/mail_dir/REJECTED ', in JSON format.

5. Block request made using a list of disposable e-mails domains (non-exhaustive list). Please see ' [src/var/untrusty_domains/disposable_email_domains.php](https://github.com/BarbeRousseLibre/hermessenger/blob/master/src/var/untrusty_domains/disposable_email_domains.php) ', thus they are moved into ' var/mail_dir/UNTRUSTY/DISPOSABLE '.

6. Sensitives datas, which is your ISP's SMTP server info, are stored into ' src/.env ' to moves it from code as document root.

7. Mails file's name are using a formatted name from user's input data to quickly find out an e-mail regarding time and date, IP, firstname and second name as sender's e-mail address. As their current status : pending, accepted, rejected. See " How it works " below.

8. Mail's file's content store all data from the user input, in JSON, plus IP, date and time and if a copy was asked.

## How to install it

You can either download the source code from [github](https://github.com/BarbeRousseLibre/hermessenger/) or [packagist.org](https://packagist.org/packages/barberousselibre/hermessenger) by using [Composer](https://getcomposer.org/) (recommanded for simplicity).

### Security notes (for administators installing hermessenger):

1. Be aware that ' src/.env ' file should **NEVER** be accessible to your webserver (and so, client). This is why it is outside the document root.

2. As ' src/.env ' should stay inside ' .gitignore ' or any CVS to avoid sharing it by mistake.

3. Be still sure to **never execute** this command:
```bash
$ git add src/.env
```

Only ' src/.env.example ' is safe for sharing (and you should **NEVER** use it directly).
If that so, changes ASAP your password, maybe user as well. This is your responsability as the one implementing my script. **Git never forget**! As the internet.

4. NEVER use ' src/.env.example ', it to belong to ' src/ ' as well (outside the document root).

5. It is also useless to manually block it from your webserver, because only ' /path/to/the/document_root/hermessenger/public/ ' should be accessible to your webserver, and should be your document root **no matter what**. WIP.

6. This also apply (1., 2., 3., 4., 5.), at a lesser degree of security issue, to ' config/settings.php ' as well. See ' config/settings.example.php '.

Soon some examples of configuration using NGinx & Apache2 will be provided, as tweaks for php.ini and a dedicated pool for it later.

## How it works

Please see [HOW-IT-WORKS.md](https://github.com/BarbeRousseLibre/hermessenger/tree/master/docs/HOW-IT-WORKS.md) for more details.

## How to use it

You need a working mail service, or ESP allowing you to use their SMTP servers to send the e-mail with PHPMailer.

Once you have these info, simply follow instruction into ' How to install it ' above.

Please see [HOW-TO-USE-IT.md](https://github.com/BarbeRousseLibre/hermessenger/tree/master/docs/HOW-TO-USE-IT.md) for more details.

### About some files

Please see [ABOUT-FILES.md](https://github.com/BarbeRousseLibre/hermessenger/tree/master/docs/ABOUT-FILES.md) for more details.

## Special thanks to… and credit

- Everyone's helping me by answering my answers or showing what was bad, you are great ! Special thanks to #php@irc.libera.chat and « Les joies du code » Discord server for their advice and patience.

- [PHP](https://www.php.net) for their documentation.

- [KDevelop](https://kdevelop.org/) for being a neat IDE.

- [PHPMailer](https://github.com/PHPMailer/PHPMailer) for the ease to install and the good work on sending mails.

- [PHPDotenv from vlucas](https://github.com/vlucas/phpdotenv) for making this works more secure, easily.

- [Justine MULLER](https://justine-muller.fr) for her advice and guidance on HTML.

- [Blu](https://www.instagram.com/bluareus/) for her cute logo she made pretty quickly!

- [Les esprits atypiques](https://les-esprits-atypiques.org) for giving me a good case to learn PHP.

## Last words

I can not push this more, but… **THIS IS NOT READY FOR PRODUCTION USE**. If you do it, well… 

Feel free to help me improves it, I'm open to critics (if they are constructive).

Just keep in mind : There is obviously better project around, doing this, with better or more modern way. This is one of my goal : Improves it to reach the " perfection ".

Thanks, stay safe.

GASPARD DE RENEFORT Kévin
