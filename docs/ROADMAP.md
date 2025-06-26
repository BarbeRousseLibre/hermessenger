# Roadmap

These goals could change.

## Notes about versioning

Since I'm learning, you could see very unusual or bad behavior from my own contribution, branches as tag versionning is non consistent as it is.

In futur releases, this will be followed more strictly, [semantic versioning](https://semver.org/).

For now, all 0.1.x is poorly using tagging and branches system.

**Please be sure to use the last tag available for the last " stable " release for any testing.**

## Current version

After months of testing and (re)learning to use my favorites tools to do so, I managed to get a working form that seems to be reliable.

As for now, the code is still not ready for a production environement. I will use it for my personal needs and see what is bad or need to be improved.

## Futur goal

Non-exhaustiv lists of features I want and need to add.

### Spam, AI & Bots strategy

The actual system to avoid spam is divided between a sets of array listing the non-allowed domains used as disposable mailbox and a slow rate sending
to avoid effecient attacks against a mailbox or ESP services.

This is way under the expected protection I want for this tool. For now, at best, it mitigate attacks.

Against this, it is intended to use solutions as [Anubis](https://xeiaso.net/blog/2025/anubis/), avoiding to use some services such as Cloudflare.

This will help by « weighting » the soul of request mades to access, from the web browser, the mail form (Hermessenger).

Again, this won’t and never will be a 100% proof solution, as far as I know, it’s another layer.

### Phone numbers
I realized Hermessenger could not handle properly, for now, the case of phone number. We could simply add a input in the HTML source code and retrieve
the phone numbers, but regarding the country the size and format differs. 

Instead of doing this from scratch, I’ll use a library made to manager every international phone numbers format.
