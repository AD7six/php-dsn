# PHP DSN

A utility for parsing and generating service DSNs

## Basic usage

The main dsn class implements a parse function which returns a dsn instance:

    use \AD7six\Dsn\Dsn;

    $url = $_ENV['SOME_SERVICE_URL'];
    $dsn = Dsn::parse($serviceUrl);

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

## Dsn Wrapper classes

Wrapper classes allow developers to adapt a dsn to their own specific usage.

## References

[12 factor applications][1]

 [1]: http://12factor.net/
