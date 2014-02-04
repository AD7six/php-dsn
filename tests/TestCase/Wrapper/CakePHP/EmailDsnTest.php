<?php

namespace AD7six\Dsn\Test\TestCase\Wrapper\CakePHP;

use \AD7six\Dsn\Wrapper\CakePHP\EmailDsn;
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
                'mail://localhost/?from=you@localhost',
                [
                    'transport' => 'Mail',
                    'host' => 'localhost',
                    'from' => 'you@localhost'
                ]
            ],
            [
                'smtp://user:secret@localhost:25/?from[site@localhost]=My+Site&timeout=30',
                [
                    'transport' => 'Smtp',
                    'host' => 'localhost',
                    'port' => 25,
                    'username' => 'user',
                    'password' => 'secret',
                    'from' => ['site@localhost' => 'My Site'],
                    'timeout' => 30,
                ]
            ],
            [
                'smtp://user:secret@localhost:25/?from=you@localhost&messageId=true&template=0&layout=0&timeout=30',
                [
                    'transport' => 'Smtp',
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
            ]
        ];
    }
}
