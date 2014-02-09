QuRedirLogin
============

zfcuser redirect

Require
=====
ZfcUser https://github.com/ZF-Commons/ZfcUser


Install
=====

Add in application.config.php

```php
    	'Application',
        'ZfcBase',
        'ZfcUser',
 	'QuRedirLogin',
```

Usage
=====

Instace in config modules

```php
   'QuRedirectLogin' => array(
	//NAMESPACE
	//Ex. 'QuContent' => true
        '' => true
    ),
```
