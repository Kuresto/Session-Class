Session Class
=============

This is a fork from the original from [Richard Castera] (https://github.com/rcastera/Session-Class), updating its functionalities to better acomodate some of my needs. Soon I will add more relevant elements to its documentation.

### Setup
-----------------
 Add a `composer.json` file to your project:

```javascript
{
  "require": {
      "kuresto/session": "0.1.0"
  }
}
```

Then provided you have [composer](http://getcomposer.org) installed, you can run the following command:

```bash
$ composer.phar install
```

That will fetch the library and its dependencies inside your vendor folder. Then you can add the following to your
.php files in order to use the library (if you don't already have one).

```php
require 'vendor/autoload.php';
```
