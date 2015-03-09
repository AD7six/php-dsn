<?php

namespace AD7six\Dsn\Test\TestCase\Wrapper\CakePHP\V3;

use \AD7six\Dsn\Wrapper\CakePHP\V3\EmailDsn;
use \PHPUnit_Framework_TestCase;

/**
 * EmailDsnTest
 *
 */
class EmailDsnTest extends PHPUnit_Framework_TestCase
{

/**
 * testDefaults
 *
 * @dataProvider defaultsProvider
 * @return void
 */
    public function testDefaults($url, $expected)
    {
        $dsn = new EmailDsn($url);

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
                'mail://user:secret@localhost:25/?timeout=30&client=&tls=',
                [
                    'className' => 'Mail',
                    'host' => 'localhost',
                    'port' => 25,
                    'username' => 'user',
                    'password' => 'secret',
                    'timeout' => 30,
                    'client' => '',
                    'tls' => ''
                ]
            ],
            [
                'smtp://user:secret@localhost:25/?from[site@localhost]=My+Site&timeout=30',
                [
                    'className' => 'Smtp',
                    'host' => 'localhost',
                    'port' => 25,
                    'username' => 'user',
                    'password' => 'secret',
                    'from' => ['site@localhost' => 'My Site'],
                    'timeout' => 30,
                ]
            ],
            [
                'smtp://user:secret@localhost:25/?from=you@localhost&messageId=1&template=0&layout=0&timeout=30',
                [
                    'className' => 'Smtp',
                    'host' => 'localhost',
                    'port' => 25,
                    'username' => 'user',
                    'password' => 'secret',
                    'from' => 'you@localhost',
                    'messageId' => true,
                    'template' => false,
                    'layout' => false,
                    'timeout' => 30,
                ]
            ],
            [
                'smtp://user:secret@ssl%3A%2F%2Flocalhost:465/?from=you@localhost&messageId=1&template=0&layout=0&timeout=30',
                [
                    'className' => 'Smtp',
                    'host' => 'ssl://localhost',
                    'port' => 465,
                    'username' => 'user',
                    'password' => 'secret',
                    'from' => 'you@localhost',
                    'messageId' => true,
                    'template' => false,
                    'layout' => false,
                    'timeout' => 30,
                ]
            ]
        ];
    }
}
