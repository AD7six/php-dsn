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

	public function testAllByPrefix() {
		$dummyDsn = [
			'FOO' => 'ignored',
			'FOO_URL' => 'service://host/path',
			'FOO_BAR_URL' => 'service://host/path',
			'FOO_BEE_URL' => 'not a url',
			'FOO_BAR_URL_MORE' => 'ignored',
		];

		$class = $this->getMockClass('\AD7six\Dsn\Dsn', ['_allDsnVars']);
		$class::staticExpects($this->once())
			->method('_allDsnVars')
			->will($this->returnValue($dummyDsn));

		$expected = [
			'BAR' => 'service://host/path',
			'BEE' => 'not a url',
			'default' => 'service://host/path',
		];
		$this->assertSame($expected, $class::allByPrefix('FOO'));
	}

	public function testParsePrefix() {
		$dummyDsn = [
			'FOO' => 'ignored',
			'FOO_URL' => 'service://host/path',
			'FOO_BAR_URL' => 'service://host/path',
			'FOO_BEE_URL' => 'not a url',
			'FOO_BAR_URL_MORE' => 'ignored',
		];

		$class = $this->getMockClass('\AD7six\Dsn\Dsn', ['_allDsnVars']);
		$class::staticExpects($this->once())
			->method('_allDsnVars')
			->will($this->returnValue($dummyDsn));

		$expected = [
			'bar' => [
				'scheme' => 'service',
				'host' => 'host',
				'path' => '/path'
			],
			'default' => [
				'scheme' => 'service',
				'host' => 'host',
				'path' => '/path'
			]
		];
		$this->assertSame($expected, $class::parsePrefix('FOO'));
	}
}
