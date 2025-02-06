# How it works

Still a WIP.

## Take the user's input and saves it into a JSON

First it checks validity of the user input and write a file in JSON format if all tests are OK or not and if the needed directories exist:

> {

>    "firstname": "Foo",

>    "secondname": "BAR",

>    "email": "foo.bar@gogole.com",

>    "subject": "This is a test subject",

>    "body": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit  in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laboru",

>    "IP": "XXX.XX.XXX.XXX",

>    "date_and_time": "YYYY-MM-DD_hhmmss",

>    "send_copy": bool

> }

All values are strings, beside "send_copy" value that is a bool (so no double-quoted value for the bool).

The mail file will take this kind of naming:

> status_mail_YYYY-MM-DD_hhmmss_XXX.XXX.XX.XXX_firstname_secondname_recipient-mailbox_at_domain.tld.json

Where:
- **status_mail**: pending, accepted, rejected. The actual mail's status regarding the current workflow.

- **_**: Field delimiter between each value (listed here), replace also white-space characters when needed.

- **YYYY-MM-DD**: date in the international format.

- **hhmmss**: time, 24h format, with seconds.

- **XXX.XXX.XX.XXX**: the IP sending the request, which is **not a trusty** value.

- **firstname**: the value inside the user's input. An underscore ('_', a field delimiter) is replacing the following white space.

- **secondname**: the value inside the user's input.

- **recipient-mailbox**: the name of the mailbox in the user's input.

- **at**: the '@' (at) is replaced by '\_at\_' to avoid error in Unix's filesystem.

- **domain.tld**: the ESP domain holding 'recipient-mailbox' user.

- **.json**: Type of the file (JSON).

This file will be stored into ' var/mail_temp_directory/ ' as a JSON.

Per default, this is not sending any mail at this point. 

This was written to be used aside a task schedular (such as a cronjob), a loop or you can (for testing purpose) execute the following file manually on a shell : ' bin/send_mail_in_queue.php ':

### Crontask job:

1. Be sure to target the crontask file from an user with the permission to use ' /usr/bin/phpX.X ' binary.

2. This user should have the read and execution permissions on the document root's parent as the ' bin/send_mail_in_queue.php ' script.

3. Before actually settings this into your user's crontab, try executing it manually, see point ' PHP-CGI manual sending / testing '. below.

The output could be redirected to some logs file, per default you could use ' logs/ ' directory. So the user should as well be able to read, write and execute into this directory.

**Note:** If into ' src/php_mailer.php ' you turned on the debug features, expect dozens of line for each sending, instead of a few, as this example showing when debug features is off:

```Mail to send:
=> " mail_pending_2024-11-14_130849_192.168.1.44_Kévin_GASPARD_DE_RENEFORT_misc_at_koshie.fr.json "
Message has been sent the 2024-11-14, at 13:09:09 and file was copied to /path/to/hermessenger/var/mail_dir/ACCEPTED/
```

Open your user's crontab the **good way**:
```bash
$ crontab -u youruser -e
```

[Do not manually open and edit it !](https://linux.die.net/man/1/crontab)
> (…)
> Each user can have their own crontab, and though these are files in /var/spool/ , they are not intended to be edited directly.
> (…)

Saying this, it is your server, not mine…

Then add (for a sending every 5 minutes):

```
*/5 * * * * /usr/bin/php8.2 /path/to/hermessenger/bin/send_mail_in_queue.php >> /path/to/hermessenger/logs/sending_logs.txt
```

Or every minutes:
```
* * * * * /usr/bin/php8.2 /path/to/hermessenger/bin/send_mail_in_queue.php >> /path/to/hermessenger/logs/sending_logs.txt
```

*Do not forget to save and quit, otherwise this won't be applied until so !*

### PHP-CGI manual sending / testing:

Simply execute the following command:

```bash
$ /usr/bin/php8.2 /path/to/hermessenger/bin/send_mail_in_queue.php >> /path/to/hermessenger/logs/sending_logs.txt
```

To be sure it will works when using a crontab (see above, ' Crontask job '), hit the full path for your PHP binary and proper release if needed. As would do your cron task job. If it works from the shell (with full path to binary) **with the same user that will run the crontask job**, it *should* works from crontab. If it is not, try to test your crontab.

### Bash loop using while (300 seconds, so 5 minutes):
```bash
$ while true; do /usr/bin/php8.2 /path/to/hermessenger/bin/send_mail_in_queue.php >> /path/to/hermessenger/logs/sending_logs.txt; sleep 300; done
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

## The pending queue process

The goal is to make impossible to overload your mailbox, or being blocked by your ESP's SMTP server for abusing it without knowing it (sending limit ratio was reached). To setup the sending rate from Hermessenger, you should look up what's your sending limits and imagine the following scenario:

If someone manage to abuse this form by adding 10k mails into your mail pending queue, that won't block you in any way (beside your web server being attacked with a denial of services).
If you set a call to ' bin/send_mail_in_queue.php ' every 5 minutes, it means you won't send more than 60 ÷ 5 = 12 mails per hours, 12 × 24 = 288 mails per day.
**Warning : This has to be doubled if you wants to allow your user to get a receipt / copy on their own mailbox, making it to 576 e-mails sends / day.**

For a mail per minute, 60 minutes × 24 hours = 1440 mail's sending, doubled to 2880 per day with all request made to get a copy.

**This is your job to define what's the maximum limit for the mail's sending per hour**. You should also **keep in mind that leaving a safe difference between what could be sended per day by Hermessenger and what's your actual limit ration** is important: This domains, probably sharing among different mailbox, this ratio should still be able to sends mails.

## The sending process

Once ' bin/send_mail_in_queue.php ' is called, it will look up into ' var/temp_mail_directory ' and report the oldest mail file (if any) with desired prefix (being hardcoded, for now, ' mail_pending_ ', WIP!). Once one mail has been found, it will try to actually send the mail. It will ignore '.', '..' (Unix path) as '.gitkeep' or other alike files. If no mails are pending, starting (still hardcoded for now) with ' mail_pending_ ', it simply exit.

## After sending 

Regarding the returned code from PHPMailer, will move the mail file into ' var/mail_dir/ACCEPTED ' or ' var/mail_dir/REJECTED '.

**It's important to note : If PHPMailer return true, it does NOT MEANS YOUR MAIL IS ACTUALLY SENDED TO THE MAILBOX. The recipient server could reject it for many reasons.**

To find out the reason, enable debug's feature inside ' src/php_mailer.php '.

The mail file is also renamed regarding it's status (returned by PHPMailer), ' accepted_mail ' or ' rejected_mail ' in the expected directory.

This allows you to keep a trace of every message, even if they are removed from the mailbox, as logs and helping you getting statistic from file's name and it's content, and finally detect non-sended message (from PHPMailer side only, so your own side).
