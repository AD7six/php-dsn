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
		$expected = [
			'scheme' => 'service',
			'host' => 'host',
			'path' => '/path'
		];
		$return = Dsn::parseUrl('service://host/path');
		$this->assertSame($expected, $return, 'a basic url should be parsed');

		$expected = [
			'scheme' => 'mysql',
			'host' => 'localhost',
			'port' => 3306,
			'user' => 'user',
			'pass' => 'password',
			'path' => '/database_name',
		];
		$return = Dsn::parseUrl('mysql://user:password@localhost:3306/database_name');
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
		$return = Dsn::parseUrl('mysql://user:password@localhost:3306/database_name?encoding=utf8&flags=0');
		$this->assertSame($expected, $return, 'Option (get arguments) should be merged with the parsed url');
	}

}
