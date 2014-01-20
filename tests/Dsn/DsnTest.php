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
 * @return void
 */
	public function testParseUrl() {
		$dsn = new Dsn('');

		$expected = [
			'scheme' => 'service',
			'host' => 'host',
			'path' => '/path'
		];
		$dsn->parseUrl('service://host/path');
		$return = $dsn->toArray();

		$this->assertSame($expected, $return, 'a basic url should be parsed');

		$expected = [
			'scheme' => 'mysql',
			'host' => 'localhost',
			'port' => 3306,
			'user' => 'user',
			'pass' => 'password',
			'path' => '/database_name',
		];
		$dsn->parseUrl('mysql://user:password@localhost:3306/database_name');
		$return = $dsn->toArray();
		$this->assertSame($expected, $return, 'A url should be parsed into it\'s component parts');

		$expected = [
			'scheme' => 'mysql',
			'host' => 'localhost',
			'port' => 3306,
			'user' => 'user',
			'pass' => 'password',
			'path' => '/database_name',
			'encoding' => 'utf8',
			'flags' => '0',
		];
		$dsn->parseUrl('mysql://user:password@localhost:3306/database_name?encoding=utf8&flags=0');
		$return = $dsn->toArray();
		$this->assertSame($expected, $return, 'Option (get arguments) should be merged with the parsed url');
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
