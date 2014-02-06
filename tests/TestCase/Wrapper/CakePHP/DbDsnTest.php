<?php

namespace AD7six\Dsn\Test\TestCase\Wrapper\CakePHP;

use \AD7six\Dsn\Wrapper\CakePHP\DbDsn;
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
 * testPlugins
 *
 * @dataProvider pluginsProvider
 * @return void
 */
    public function testPlugins($url, $expected)
    {
        $dsn = new DbDsn($url);

        $return = $dsn->toArray();
        $this->assertSame($expected, $return, 'The url should parse as expected');

        $return = $dsn->__toString();
        $this->assertSame($url, $return, 'The dsn should parse back to the same url');
    }


    public function pluginsProvider()
    {
        return [
            [
                'mongo+MongoDb.MongodbSource://user:password@localhost/test_database_name',
                [
                    'datasource' => 'MongoDb.MongodbSource',
                    'adapter' => 'MongoDb.MongodbSource',
                    'host' => 'localhost',
                    'port' => 27017,
                    'login' => 'user',
                    'password' => 'password',
                    'database' => 'test_database_name',
                ]
            ],
            [
                'mysql+Plugin.DboClass://user:password@localhost/database_name',
                [
                    'datasource' => 'Plugin.DboClass',
                    'adapter' => 'Plugin.DboClass',
                    'host' => 'localhost',
                    'port' => 3306,
                    'login' => 'user',
                    'password' => 'password',
                    'database' => 'database_name'
                ]
            ],
            [
                'mysql+Plugin.Directory.DboClass://user:password@localhost/database_name',
                [
                    'datasource' => 'Plugin.Directory/DboClass',
                    'adapter' => 'Plugin.Directory.DboClass',
                    'host' => 'localhost',
                    'port' => 3306,
                    'login' => 'user',
                    'password' => 'password',
                    'database' => 'database_name'
                ]
            ],
            [
                'mysql+Plugin.Directory.SubDirectory.DboClass://user:password@localhost/database_name',
                [
                    'datasource' => 'Plugin.Directory/SubDirectory/DboClass',
                    'adapter' => 'Plugin.Directory.SubDirectory.DboClass',
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
