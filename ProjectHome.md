Summary:

> php-svn-fetcher is a useful tool to fetch the svn commit log and store them at mysql database.

> When you get the data, you can do anything you want.

Install:
> please rename **cronjob/svn-fetcher-sample.ini** to **svn-fetcher.ini** and update its configuration.

Requirement:

> Install **svn**, **pdo**, **pdo-mysql** from **PECL** before you running this script.

Cronjob Example:

```
  */10 9-18 * * *    /usr/bin/php svn-fetcher.php 
```

Advance:

> More useful function: [PHP: SVN - Manual](http://www.php.net/manual/en/book.svn.php)