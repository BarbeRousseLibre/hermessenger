# Roadmap

These goals could change, but more or less I aim:

## Notes about versioning

Since I'm learning, you could see very unusual or bad behavior from my own contribution, branches as tag versionning is non consistent as it is.

In futur releases, this will be followed more strictly, [semantic versioning](https://semver.org/).

For now, all 0.1.x is poorly using tagging and branches system.

**Please be sure to use the last tag available for the last " stable " release for any testing.**

## Futur release (0.2)

- Add: removes, add or modify easily the needed input from a HTML form (\<input/\>, \<textarea\>\</textarea\>, etc).
- Add: feature to add easily a list of subject that user could pick up from, prefixing the actual mail's subject for an easier and quicker way to read subject category or simplify the filtering process for a MUA.
- Add: captcha to block AI / bot, while still being accessible for some disabled user (such as blind peoples, or with dyslexia, etc).

- Enhancing: protection against floods and mail-bombing, as mitigate the possibility of fill a disk space with request until there is no more room left.

- Check: Compatibility with all version of PHP8
- Check: Better use of PHPDotenv (using [cache](https://github.com/vlucas/phpdotenv/issues/207))

## Futur release (0.3)

- Add: Non-latin alphabets support.
- Add: An easy I18N way.
- Add: Content parsing for suspicious wording, links, etc.

## Futur release (0.4)

- Add: sharing the full testing process.

- Enhancing: a better listing system for blacklisted domains (disposable) as IP and mailbox.

## Futur release (0.5)

- Enhancing: being closer to the [Twelve-Factor App](https://12factor.net/), without necessary fully respect it, until I'm ready to do so.

## Futur release (1.x)

Incompatibility that is predicted for the first stable release:

- Add: A safer, easier and quicker configuration system, avoiding administrators to edit actual PHP code.
