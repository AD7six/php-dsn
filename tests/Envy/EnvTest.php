<?php

namespace AD7six\Envy\TestCase;

use \AD7six\Envy\Env;
use \PHPUnit_Framework_TestCase;

/**
 * EnvTest
 *
 */
class EnvTest extends PHPUnit_Framework_TestCase {

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
		$return = Env::parseUrl('service://host/path');
		$this->assertSame($expected, $return, 'a basic url should be parsed');

		$expected = [
			'scheme' => 'mysql',
			'host' => 'localhost',
			'port' => 3306,
			'user' => 'user',
			'pass' => 'password',
			'path' => '/database_name',
		];
		$return = Env::parseUrl('mysql://user:password@localhost:3306/database_name');
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
		$return = Env::parseUrl('mysql://user:password@localhost:3306/database_name?encoding=utf8&flags=0');
		$this->assertSame($expected, $return, 'Option (get arguments) should be merged with the parsed url');
	}

	public function testAllByPrefix() {
		$dummyEnv = [
			'FOO' => 'ignored',
			'FOO_URL' => 'service://host/path',
			'FOO_BAR_URL' => 'service://host/path',
			'FOO_BEE_URL' => 'not a url',
			'FOO_BAR_URL_MORE' => 'ignored',
		];

		$class = $this->getMockClass('\AD7six\Envy\Env', ['_allEnvVars']);
		$class::staticExpects($this->once())
			->method('_allEnvVars')
			->will($this->returnValue($dummyEnv));

		$expected = [
			'BAR' => 'service://host/path',
			'BEE' => 'not a url',
			'default' => 'service://host/path',
		];
		$this->assertSame($expected, $class::allByPrefix('FOO'));
	}

	public function testParsePrefix() {
		$dummyEnv = [
			'FOO' => 'ignored',
			'FOO_URL' => 'service://host/path',
			'FOO_BAR_URL' => 'service://host/path',
			'FOO_BEE_URL' => 'not a url',
			'FOO_BAR_URL_MORE' => 'ignored',
		];

		$class = $this->getMockClass('\AD7six\Envy\Env', ['_allEnvVars']);
		$class::staticExpects($this->once())
			->method('_allEnvVars')
			->will($this->returnValue($dummyEnv));

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
