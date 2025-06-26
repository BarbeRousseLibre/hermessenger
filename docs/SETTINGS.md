# How to set up settings.php and make Hermessenger works for you

After installingr Hermessenger, you need to configure it accordingly to your web server parameters.

This settings.php file will allow you to edit:

- Characters encoding
- Timezone
- UNIX path within your server
- URLs used for redirection (on valid or rejected mail sending request)

## Characters encoding

Please see php.net/manual/en/mbstring.supported-encodings.php.

## Timezone

Please see php.net/manual/en/timezones.php.

## Document Root parent

Hermessenger needs to know where is the _parent_ of the Document Root, so if your actual Document Root is:

```/var/www/localhost/website.org/htdocs```

Then, this has to be set to:

```/var/www/localhost/website.org```

**This path should NOT have an ending slash ("/")**.

## Redirecting location

When the client will click the submit button to send the message, two cases are possibles:

- Valid sending, the mail has been accepted by the SMTP server once Hermessenger has itself validate it.\*

- Rejected sending, the mail won't be sended to the SMTP server because Hermessenger could not accept it, bad formated message for example.

You have to define:

- The scheme, being 'http' or 'https', only.
- The domain name, being the website holding the mail form.
- The accepted ressource, being the HTML page the client will be redirected on in case of success.
- The rejected ressource, being the HTML page the client will be redirected on in case of failure.

Example:

```
$redir_location =
[
    "scheme"                       => "https",
    "domain_name"                  => "website.org",
    "ressource_path_accepted"      => "valid_sending.html",
    "ressource_path_rejected"      => "rejected_sending.html"
];
```
