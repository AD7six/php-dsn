<?php

namespace AD7six\Dsn\Test\TestCase\Wrapper\CakePHP\V2;

use \AD7six\Dsn\Wrapper\CakePHP\V2\LogDsn;
use \PHPUnit_Framework_TestCase;

/**
 * LogDsnTest
 *
 */
class LogDsnTest extends PHPUnit_Framework_TestCase
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
        if (!defined('LOGS')) {
            define('LOGS', '/some/tmp/path');
        }

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
        $dsn = new LogDsn($url);

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
                'file:///LOGS/?types=notice,info,debug&file=debug',
                [
                    'engine' => 'File',
                    'path' => LOGS,
                    'types' => ['notice', 'info', 'debug'],
                    'file' => 'debug'
                ]
            ],
            [
                'file:///LOGS/?types=warning,error,critical,alert,emergency&file=error',
                [
                    'engine' => 'File',
                    'path' => LOGS,
                    'types' => ['warning', 'error', 'critical', 'alert', 'emergency'],
                    'file' => 'error'
                ]
            ]
        ];
    }
}
