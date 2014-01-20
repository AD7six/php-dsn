<?php

namespace AD7six\Dsn\TestCase;

use \AD7six\Dsn\Dsn;
use \PHPUnit_Framework_TestCase;

/**
 * DsnTest
 *
 */
class DsnTest extends PHPUnit_Framework_TestCase {

/**
 * testParseUrl
 *
 * @dataProvider parseUrlProvider
 * @return void
 */
	public function testParseUrl($url, $expected) {
		$dsn = new Dsn($url);
		$return = $dsn->toArray();
		$this->assertSame($expected, $return, 'The url should parse as expected');
	}

	public function parseUrlProvider() {
		return [
			[
				'service://host/path',
				[
					'scheme' => 'service',
					'host' => 'host',
					'path' => '/path'
				]
			],
			[
				'mysql://user:password@localhost:3306/database_name',
				[
					'scheme' => 'mysql',
					'host' => 'localhost',
					'port' => 3306,
					'user' => 'user',
					'pass' => 'password',
					'path' => '/database_name',
				]
			],
			[
				'mysql://user:password@localhost:3306/database_name?encoding=utf8&flags=0',
				[
					'scheme' => 'mysql',
					'host' => 'localhost',
					'port' => 3306,
					'user' => 'user',
					'pass' => 'password',
					'path' => '/database_name',
					'encoding' => 'utf8',
					'flags' => '0',
				]
			]
		];
	}

	public function testKeyMaps() {
		$url = 'mysql://user:password@localhost:3306/database_name';
		$dsn = new Dsn($url, ['user' => 'username', 'pass' => 'password']);

		$expected = [
			'scheme' => 'mysql',
			'host' => 'localhost',
			'port' => 3306,
			'username' => 'user',
			'password' => 'password',
			'path' => '/database_name',
		];

		$return = $dsn->toArray();
		$this->assertSame($expected, $return, 'Translated keys should be used in the output');

		$this->assertSame('password', $dsn->password, 'The translated key should be accessible');
		$this->assertNull($dsn->pass, 'The original key should act like it does not exist');
	}

/**
 * testBidirectionalUrl
 *
 * @dataProvider bidirectionalProvider
 * @param string $url
 * @return void
 */
	public function testBidirectionalUrl($url)  {
		$dsn = new Dsn($url);
		$this->assertSame($url, (string) $dsn, 'The regenerated url should be identical to the input');
	}

/**
 * bidirectionalProvider
 *
 * @return array
 */
	public function bidirectionalProvider() {
		return [
			['service://host/path'],
			['mysql://user:password@localhost:3306/database_name'],
			['mysql://user:password@localhost:3306/database_name?encoding=utf8&flags=0']
		];
	}

}
