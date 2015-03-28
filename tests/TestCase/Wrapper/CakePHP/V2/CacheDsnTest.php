<?php

namespace AD7six\Dsn\Test\TestCase\Wrapper\CakePHP\V2;

use \AD7six\Dsn\Wrapper\CakePHP\V2\CacheDsn;
use \PHPUnit_Framework_TestCase;

/**
 * CacheDsnTest
 *
 */
class CacheDsnTest extends PHPUnit_Framework_TestCase
{

/**
 * __construct
 *
 * Not setupBeforeClass because, surprisingly, that's too late if the first test
 * uses a data provider
 *
 * @param string $name
 * @param array $data
 * @param string $dataName
 */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!defined('CACHE')) {
            define('CACHE', '/some/tmp/path');
        }

        $_ENV['APP_NAME'] = 'test_app';

        parent::__construct($name, $data, $dataName);
    }

/**
 * testDefaults
 *
 * @dataProvider defaultsProvider
 * @return void
 */
    public function testDefaults($url, $expected)
    {
        $dsn = new CacheDsn($url);
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
                'file:///CACHE/?prefix=APP_NAME_&duration=DURATION',
                [
                    'engine' => 'File',
                    'path' => CACHE,
                    'prefix' => 'test_app_',
                    'duration' => '+999 days',
                ]
            ],
            [
                'file:///CACHE/?prefix=APP_NAME_cake_core_&duration=DURATION',
                [
                    'engine' => 'File',
                    'path' => CACHE,
                    'prefix' => 'test_app_cake_core_',
                    'duration' => '+999 days',
                ]
            ],
            [
                'file:///CACHE/?prefix=APP_NAME_cake_model_&duration=DURATION',
                [
                    'engine' => 'File',
                    'path' => CACHE,
                    'prefix' => 'test_app_cake_model_',
                    'duration' => '+999 days',
                ]
            ],
            [
                'redis://user:password@hostname?prefix=APP_NAME_&duration=DURATION',
                [
                    'engine' => 'Redis',
                    'servers' => 'hostname',
                    'user' => 'user',
                    'password' => 'password',
                    'prefix' => 'test_app_',
                    'duration' => '+999 days',
                ]
            ]
        ];
    }
}
