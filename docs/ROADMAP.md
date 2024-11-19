# Roadmap

These goals could change, but more or less I aim:

## Futur release (0.2)

- Allowing a list of prefixed subject within a list on the HTML contact form, helping recipient (for example) to use MUA (such as Thunderbird) filters more easily and giving user an idea of the kind of questions are expected.

- Add, removes or modifiy in a simple maner the HTML fields expected.

- Rely on a configuration file that would greatly ease the settings and installation of Hermessenger, without actually modifying any source code file (such as ' config/variables.php ').

- Compatibility to PHP 8.3.

- Captcha to block AI / bot, while still being accessible for some disabled user (such as blind peoples, or with dyslexia, etc).

- Better use of PHPDotenv (using [cache](https://github.com/vlucas/phpdotenv/issues/207))

- Protection against floods and mail-bombing: It is nice to avoid your mailbox to be full, but this would be even better if the web server hosting Hermessenger and the temporary datas (mail_pending_\* JSON files) could not be fulled too.

## Futur release (0.3)

- Non-latin alphabets support.

- An easy I18N way.

- Content parsing for suspicious wording, links, etc.

## Futur release (0.4)

- A better listing system for blacklisted domains (disposable) as IP and mailbox.

- Sharing the full testing process.

## Futur release (0.5)

- Being closer to the [Twelve-Factor App](https://12factor.net/)
