# PHP DSN #ARCHIVED#

A utility for parsing and generating service DSNs

## What is a DSN?

A [data source name](http://en.wikipedia.org/wiki/Data_source_name) (DSN) is a string which defines how to connect to a service. Since it's a string, it's portable, not language or implementation dependent and anything capable of parsing it can know how to connect to the service it points at.

## What does this repo do?

This repo only provides a means of converting a DSN string into an array and vice versa.

## Wrapper classes?

When using a wrapper class, this repo provides an array _in the format expected_ by a particular consumer. So for example, the CakePHP wrapper classes provide arrays in the format the framework understands for a given DSN string.

## Basic usage

The main dsn class implements a parse function which returns a dsn instance:

    use \AD7six\Dsn\Dsn;

    $url = $_ENV['SOME_SERVICE_URL'];
    $dsn = Dsn::parse($url);

The class of the returned object is dependent upon the scheme of the service url, for example:

    // $dsn is an instance of \AD7six\Dsn\Db\MysqlDsn;
    $dsn = Dsn::parse('mysql://host/dbname');

    // $dsn is an instance of \AD7six\Dsn\Db\SqliteDsn;
    $dsn = Dsn::parse('sqlite:///path/to/name.db');

For unknown schemes - the an instance of the called class is returned. This also means that a
more specific instance can be obtained by using a less-generic class where appropriate:

    // $dsn is an instance of \AD7six\Dsn\Dsn;
    $dsn = Dsn::parse('newdb://host/dbname');

    // $dsn is an instance of \AD7six\Dsn\DbDsn;
    $dsn = DbDsn::parse('newdb://host/dbname');

In all of the above cases, the returned instance is the "raw" dsn data:

    // $dsn is an instance of \AD7six\Dsn\Db\MysqlDsn;
    $dsn = Dsn::parse('mysql://host/dbname');

	$dsn->toArray();
	[
		'scheme' => 'mysql',
		'host' => 'host',
		'port' => 3306,
		'database' => 'dbname'
    ]

If the behavior of the raw dsn needs to be modified - use a wrapper implementation

## References

[12 factor applications][1]

 [1]: http://12factor.net/
