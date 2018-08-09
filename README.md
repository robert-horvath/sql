[![Build Status](https://travis-ci.org/robert-horvath/sql.svg?branch=master)](https://travis-ci.org/robert-horvath/sql)
[![Code Coverage](https://codecov.io/gh/robert-horvath/sql/branch/master/graph/badge.svg)](https://codecov.io/gh/robert-horvath/sql)
[![Latest Stable Version](https://img.shields.io/packagist/v/robert/sql.svg)](https://packagist.org/packages/robert/sql)

# SQL
The SQL module is a thin wrapper around PHP's [MySQLi](http://php.net/manual/en/intro.mysqli.php) extension, using the Facade Design Pattern. It executes 
[prepared statements](http://php.net/manual/en/mysqli.quickstart.prepared-statements.php) and raises [mysqli_sql_exception](http://php.net/manual/en/class.mysqli-sql-exception.php) if an error occures.

The connection settings shall be stored in php.ini file.

## Example usage
```php
namespace RHo\Sql;

try {
    $i = 10;
    $s = 'text';
    
    $db = new MySql();
    $db->prepareWithParam('CALL `db`.`stored_proc`(?,?)', 'is', $i, $s);
    $arr = $db->execute();

    var_dump($arr);
} catch (\mysqli_sql_exception $e) {
    // SQL exception
}
```
## The above example might output
```
array(1) {
  [0] =>
  class stdClass#574 (2) {
    public $id =>
    int(30)
    public $value =>
    string(12) "Hello World!"
  }
}
```