NestPHP
=======

Very small, procedural, closure-based microframework

Basic Usage
===========
The quickest way to use NestPHP is to place nest.php in your site's document root, e.g., ``/var/www/html/``, then put the following in your ``.htaccess`` file or virtualhost container:
```apache
php_value auto_prepend_file /var/www/html/nest.php
```
  
To make sure it's working, create ``index.php`` with the following code:
```php
<?php
\nest\get(function() {
    echo "Hello, Nest!";
});
```

Filters
=======
NestPHP allows you to organize your code into closures, which you can chain together in order to process requests. to see this in action, create the following scripts in the following directories under your document root:

```php
// /before.php
<?php
\nest\get(function() { echo "BEFORE (root)<br>\n"; });
  
// /admin/before.php
<?php
\nest\get(function() { echo "BEFORE (admin)<br>\n"; });

// /admin/index.php
<?php
\nest\get(function() { echo "index!<br>\n"; });

// /admin/after.php
<?php
\nest\get(function() { echo "AFTER (admin)<br>\n"; });
  
// /after.php
<?php
\nest\get(function() { echo "AFTER (root)<br>\n"; });

```

If you then request ``/admin/index.php`` from your site, you'll see the following HTML:
```html
BEFORE (root)<br>
BEFORE (admin)<br>
index!<br>
AFTER (admin)<br>
AFTER (root)<br>
```

NestPHP will examine the path of the requested script, and essentially auto_prepend any ``before.php`` scripts that exist, and auto_append any ``after.php`` scripts that exist.

  
What NestPHP Doesn't Do
=======================
 * Routing
 * Templating
 * Caching
 * Logging
 * Database Abstraction
 * Admin Scaffolding

It's really not much more than a slightly fancier version of "include header/footer"; basically, it's automatically nested headers and footers, with the ability to lock up your request-specific (GET/POST) logic into closures.

...more to come
