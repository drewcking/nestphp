NestPHP
=======

Very small, procedural, closure-based microframework

Usage
=====
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
      
  
