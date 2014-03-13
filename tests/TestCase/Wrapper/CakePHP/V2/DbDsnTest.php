<?php

namespace AD7six\Dsn\Test\TestCase\Wrapper\CakePHP\V2;

use \AD7six\Dsn\Dsn;
use \AD7six\Dsn\Wrapper\CakePHP\V2\DbDsn;
use \PHPUnit_Framework_TestCase;

/**
 * DbDsnTest
 *
 */
class DbDsnTest extends PHPUnit_Framework_TestCase
{

/**
 * testDefaults
 *
 * @dataProvider defaultsProvider
 * @return void
 */
    public function testDefaults($url, $expected)
    {
        $dsn = new DbDsn($url);

        $return = $dsn->toArray();
        $this->assertSame($expected, $return, 'The url should parse as expected');

        $return = $dsn->__toString();
        $this->assertSame($url, $return, 'The dsn should parse back to the same url');
    }

/**
 * defaultsProvider
 *
 * Returns an array of [string, expectedArray]
 *
 * @return array
 */
    public function defaultsProvider()
    {
        return [
            [
                'mysql://user:password@localhost/database_name',
                [
                    'datasource' => 'Database/Mysql',
                    'host' => 'localhost',
                    'port' => 3306,
                    'login' => 'user',
                    'password' => 'password',
                    'database' => 'database_name',
                ]
            ],
            [
                'mysql://user:password@localhost/test_database_name',
                [
                    'datasource' => 'Database/Mysql',
                    'host' => 'localhost',
                    'port' => 3306,
                    'login' => 'user',
                    'password' => 'password',
                    'database' => 'test_database_name',
                ]
            ]
        ];
    }

/**
 * testMapUsage
 *
 * Map custom schemes in the dsn class to mysql for the default port (convenience only)
 * Map custom schemes in the wrapper class to a custom datasources
 *
 * @dataProvider mapUsageProvider
 * @return void
 */
    public function testMapUsage($url, $expected)
    {
        Dsn::map([
            'nysql' => '\AD7six\Dsn\Db\MysqlDsn',
            'oysql' => '\AD7six\Dsn\Db\MysqlDsn',
        ]);
        DbDsn::map([
            'mongo' => 'MongoDb.MongodbSource',
            'mysql' => 'Plugin.DboClass',
            'nysql' => 'Plugin.Directory/DboClass',
            'oysql' => 'Plugin.Directory/SubDirectory/DboClass',
        ]);
        $dsn = new DbDsn($url);

        $return = $dsn->toArray();
        $this->assertSame($expected, $return, 'The url should parse as expected');

        $return = $dsn->__toString();
        $this->assertSame($url, $return, 'The dsn should parse back to the same url');
    }


    public function mapUsageProvider()
    {
        return [
            [
                'mongo://user:password@localhost/test_database_name',
                [
                    'datasource' => 'MongoDb.MongodbSource',
                    'host' => 'localhost',
                    'port' => 27017,
                    'login' => 'user',
                    'password' => 'password',
                    'database' => 'test_database_name',
                ]
            ],
            [
                'mysql://user:password@localhost/database_name',
                [
                    'datasource' => 'Plugin.DboClass',
                    'host' => 'localhost',
                    'port' => 3306,
                    'login' => 'user',
                    'password' => 'password',
                    'database' => 'database_name'
                ]
            ],
            [
                'nysql://user:password@localhost/database_name',
                [
                    'datasource' => 'Plugin.Directory/DboClass',
                    'host' => 'localhost',
                    'port' => 3306,
                    'login' => 'user',
                    'password' => 'password',
                    'database' => 'database_name'
                ]
            ],
            [
                'oysql://user:password@localhost/database_name',
                [
                    'datasource' => 'Plugin.Directory/SubDirectory/DboClass',
                    'host' => 'localhost',
                    'port' => 3306,
                    'login' => 'user',
                    'password' => 'password',
                    'database' => 'database_name'
                ]
            ]
        ];
    }
}
