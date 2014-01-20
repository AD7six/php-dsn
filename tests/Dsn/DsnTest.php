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
					'port' => null,
					'user' => null,
					'pass' => null,
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

	public function testDefaultPorts() {
		$url = 'mysql://user:password@localhost/database_name';
		$dsn = new Dsn($url, [], 3306);

		$expected = [
			'scheme' => 'mysql',
			'host' => 'localhost',
			'port' => 3306,
			'user' => 'user',
			'pass' => 'password',
			'path' => '/database_name',
		];

		$return = $dsn->toArray();
		$this->assertSame($expected, $return, 'Default port should be in the parsed result');

		$this->assertSame($url, (string) $dsn, 'The regenerated url should be identical to the input');
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

	public function testParseNoClass() {
		$dsn = Dsn::parse('service://host/path');
		$this->assertInstanceOf('AD7six\Dsn\Dsn', $dsn);
	}

	public function testParseMysql() {
		$url = 'mysql://user:password@localhost/database_name';
		$dsn = Dsn::parse($url);
		$this->assertInstanceOf('AD7six\Dsn\MysqlDsn', $dsn);

		$expected = [
			'scheme' => 'mysql',
			'host' => 'localhost',
			'port' => 3306,
			'user' => 'user',
			'pass' => 'password',
			'database' => 'database_name',
		];

		$return = $dsn->toArray();
		$this->assertSame($expected, $return, 'Default port should be in the parsed result');

		$this->assertSame($url, (string)$dsn, 'The regenerated dsn should be the same as the input');
	}

/**
 * testParseSqlite
 *
 * @return void
 */
	public function testParseSqlite() {
		$url = 'sqlite:///over/here.db';
		$dsn = Dsn::parse($url);
		$this->assertInstanceOf('AD7six\Dsn\SqliteDsn', $dsn);

		$expected = [
			'scheme' => 'sqlite',
			'host' => null,
			'port' => null,
			'user' => null,
			'pass' => null,
			'database' => '/over/here.db',
		];

		$return = $dsn->toArray();
		$this->assertSame($expected, $return, 'Default port should be in the parsed result');

		$this->assertSame($url, (string)$dsn, 'The regenerated dsn should be the same as the input');
	}
}
