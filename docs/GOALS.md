# Goals of Hermessenger
Hermessenger aim to sends a few mails by hours, no more than 60 (one per minute), keeping futur mail's sending into a pending queue directory until a loop, a crontask job (expected behavior) or even a PHP-CGI command line execute ' bin/send_mail_in_queue.php ' script.

Every mails are stored on the web server as logs file, or backup of sended e-mails if you prefer, and moved accordingly regarding if the sending was supposed to be accepted or rejected.

It stores the untrusty e-mails in it's own directory for further analysis if desired.

It aims simplicity of use, installation, settings, while keeping you out of some problems against such tools. 

Another main objectives would be securit against possible threats. 

## No mails bombing and no flood-based denial of services (DoS)

A few possible type of threats against web mail form are explained below.

### What is mail bombing ?

The mail bombing is any actions targeting a mail server, the goal being turns it unavailable by " bombing it " by saturation, sending too many mail's request in a short period of it. Or flooding it if you prefer.

This is specially dangerous if the SMTP servers is hosted by some ESP, non self-hosted. 

Or even if the SMTP server is hosted on **your** dedicated server and if you are your own ESP, using a server hosted somewhere in a data center: The company you rent a server could detect such behavior, and simply blocks request on this port for a while.

ESP never allows an unlimited sending ratio, *at least good ones*. If they did, everyone would be able to abuse this and send a **lot** of spams.

Nobody likes spams.

### What is a flood-based denial of service (Dos) ?

A DoS (denial of service) is usually made against HTTP(S) servers: With a lot of requests per seconds, or even from a lot of different locations (DDoS, or Distributed Denial of Service), the targeted servers becomes unavailable at worst, at best if any impacts it get slowed down. They are usually hard to fight against. 

It is used to annoys peoples, make companies lost money (an online shop won't be able to sell, customers will get annoyed, etc), make the competitors unable to make profit / get a less good images, etc. What ever, it's made to turn off a service avaibility, slow it down… It's a pain.

**In the case scenario here of a web mail form system such as Hermessenger**

You installed this project (you fool !) for your personal website, nothing much (a blog for example).

## Hermessenger is a 'slow-sending' tool and that is good !

Let's imagine a simple scenario:

Some time ago, you had a bad, long and painful argument online with someone, you decided to ignore this.

This one wants to become a pain in your *ss, because he or she thinks it's fun, you deserves it, what ever.

This person see your form (after lurking online for your footprints), and realize (or guess) there is no protection against flooding message. Neat! 

There is none, you were naiv, or careless.

Now with a few commands, a (few) script(s), some script-kiddies tools, this person decide to annoys you. Simple, not very long to do too, one person could do it with the will and a bit of skills and knowledge.

**Worst**: This person is a bit more than just an annoying foe, because he or she have zombies-servers around the world. A dozen. Or have a group of annoyin' friends willing to get some fun too. 

Now you have dozens of " peoples " sending mails requests on your blog's contact form, your ESP quickly seeing this, turn off the sending because:

a/ there was really a lot in a very short period of time which is suspect at worst and at best not allowed by the ESP, or 
b/ your send-ratio per day was reached…

Both case (a or b), you are not able to sends e-mails across your whole domain. 

Another annoying case: 

c/ this is your self-hosted ESP service, you are the boss and you won't block yourself unless you are forwarding these mails to another ESP, which could blocks you or worst, adds you on a RBL services or such black list shared across **a lot** of ESP. Very annoying as well to be tagged in it.

But in this case you are sending a mail from ' mailbox1@yourownmailservice.xyz ' to ' mailbox2@yourownmailservice.xyz '. 

After a while, your own space dedicated to host your mails is full, unable to gets new mails. 

**You can't get new mails** and it doesn't stop ! You blocks IP by dozens but new requests from new IP are coming.

**Let's resume this: This person has wasted your precious time, energy and mental sanity.**

That is why this project is not made to support a lot of sending in a short period of time, instead it's holding mails for a few minutes (1-\*) to manage potential threat against your recipient's mailbox. Only **one at-a-time**, the mails would be sended.

The main and first idea here is avoiding to get a no space left scenario for legitimates e-mails into your mailbox, the second being impossible to block your domain from your SMTP e-mail service providers (such as Google, Microsoft, Infomaniak or other).

It is designed for slow sending, one at-a-time mail sending.

## Wait… the space for holding the pending mails could be full pretty quickly too !

True, for now, Hermessenger could not prevent this as it is. 

This is a feature that would need to be implemented for 0.2.

It is supposed to protect your mailbox against threats, abuse and other kind of attack and blocking, even HTTP-server side where it could act, of course, it's not preventing your from classic HTTP-(D)DoS based attack !

Nonetheless, it's pretty easy to removes them on the fly as it is now with a few tricks. A few ideas (all of them does **NOT** require root privileges):

Workaround A:

1/ Create a loop using bash built-in features ' while '

2/ At every iteration, use ' find ' command to find all json files into the ' temp_mail_directory ' and moves them into some other location, locally, or with SSH on your own machine, on an external hard drive, etc.

3/ Once the .json files has been moved, add them to a tar(.xz) archive, which is nice because compressing could remove about ~90% of disk usage, but it requires some CPU times and ressources.

4/ Per waves, once compressed, keep these archives for further analysis.

Or workaround B:

1/ Create a loop using bash built-in features ' while '

2/ Removes them on the fly if you want to get quick and dirty

Command for A, local copy and compressing them on the fly:

```
(Terminal 1) $ while true; do find /path/to/temp_mail_directory -type f -name "mail_pending_*.json" -exec mv -t /some/other/location {} + ; done
```

```
(Terminal 2) $ while true; do tar cfJ /some/other/location_for_archives/flooding_mails.tar.xz /some/other/location/*;
```

Command for A, remote copy:

```
(Local terminal)  $ while true; do find /path/to/temp_mail_directory -type f -name "mail_pending_*.json" -exec scp user@destination:/some/other/location {} + ; done
```

```
(Remote terminal) $ while true; do tar cfJ /some/other/location_for_archives/flooding_mails.tar.xz /some/other/location/*;
```

Command for B (with a safety-test first):
```
while true; do find /path/to/temp_mail_directory -type f -name "mail_pending_*.json" -exec ls {}\; done
```

If happy with the results (you are not hitting an incorrect directory, for example):
**Be very careful, this command will remove on the fly everything matching the condition of for, use ' ls ' for testing before !**
```
while true; do find /path/to/temp_mail_directory -type f -name "mail_pending_*.json" -exec rm {}\; done
```

Etcetera, there is some ways to take care of such with less pain, open to ideas :) !
