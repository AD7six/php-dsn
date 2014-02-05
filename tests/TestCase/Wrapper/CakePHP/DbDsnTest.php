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

    public function testGetEngineWithAdapter()
    {
        $dsn = new DbDsn('mysql+Plugin.DboClass://user:pass@host/database');
        $expected = 'Plugin.DboClass';
        $this->assertEquals($expected, $dsn->getEngine());

        $dsn = new DbDsn('mysql+Plugin.Directory.DboClass://user:pass@host/database');
        $expected = 'Plugin.Directory/DboClass';
        $this->assertEquals($expected, $dsn->getEngine());

        $dsn = new DbDsn('mysql+Plugin.Directory.SubDirectory.DboClass://user:pass@host/database');
        $expected = 'Plugin.Directory/SubDirectory/DboClass';
        $this->assertEquals($expected, $dsn->getEngine());
    }
}
