<?php

namespace AD7six\Dsn\Test\TestCase\Wrapper\CakePHP\V3;

use \AD7six\Dsn\Wrapper\CakePHP\V3\CacheDsn;
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
            define('CACHE', '/some/tmp/path/');
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
                'file:///CACHE/',
                [
                    'className' => 'File',
                    'path' => CACHE,
                ]
            ],
            [
                'file:///CACHE/persistent/?prefix=APP_NAME_cake_core_&duration=DURATION&serialize=1',
                [
                    'className' => 'File',
                    'path' => CACHE . 'persistent/',
                    'prefix' => 'test_app_cake_core_',
                    'duration' => '+999 days',
                    'serialize' => true,
                ]
            ],
            [
                'file:///CACHE/models/?prefix=APP_NAME_cake_model_&duration=DURATION&serialize=1',
                [
                    'className' => 'File',
                    'path' => CACHE . 'models/',
                    'prefix' => 'test_app_cake_model_',
                    'duration' => '+999 days',
                    'serialize' => true,
                ]
            ]
        ];
    }
}
