<?php

namespace AD7six\Dsn\Test\TestCase;

use \AD7six\Dsn\Dsn;
use \PHPUnit_Framework_TestCase;

class TestDsn extends Dsn {

	public function getSpecial() {
		static $fakeReturn;

		if (!$fakeReturn) {
			$fakeReturn = true;
			return 'getter value';
		}
		return $this->_url['special'];
	}

	public function setSpecial($val) {
		$this->_url['special'] = strrev($val);
	}
}

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

		$return = $dsn->__toString();
		$this->assertSame($url, $return, 'The dsn should parse back to the same url');
	}

/**
 * parseUrlProvider
 *
 * Returns an array of [string, expectedArray]
 *
 * @return void
 */
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
			],
			[
				'mysql+Fooby.DoobyDriver://user:password@localhost:3306/database_name',
				[
					'scheme' => 'mysql',
					'adapter' => 'Fooby.DoobyDriver',
					'host' => 'localhost',
					'port' => 3306,
					'user' => 'user',
					'pass' => 'password',
					'path' => '/database_name',
				]
			]
		];
	}

/**
 * testParseUrlNoUrlThrows
 *
 * @expectedException \Exception
 * @expectedExceptionMessage The url '' could not be parsed
 */
	public function testParseUrlNoUrlThrows() {
		$dsn = new Dsn();
	}

/**
 * testParseUrlNoUrlThrows
 *
 * @expectedException \Exception
 * @expectedExceptionMessage The url '' could not be parsed
 */
	public function testParseUrlNoUrlThrows2() {
		$dsn = new Dsn('');
	}

	public function testGettersAndSetters() {
		$url = 'mysql://user:password@localhost:3306/database_name';
		$dsn = new TestDsn($url);

		$dsn->host = 'somewhereelse';
		$return = $dsn->toArray();
		$expected = [
			'scheme' => 'mysql',
			'host' => 'somewhereelse',
			'port' => 3306,
			'user' => 'user',
			'pass' => 'password',
			'path' => '/database_name',
		];
		$this->assertSame($expected, $return, 'changed values should take effect in toArray');

		$dsn->special = 'tarzan';
		$return = $dsn->toArray();
		$expected = [
			'scheme' => 'mysql',
			'host' => 'somewhereelse',
			'port' => 3306,
			'user' => 'user',
			'pass' => 'password',
			'path' => '/database_name',
			'special' => 'getter value'
		];
		$this->assertSame($expected, $return, 'a bespoke getter should be called by toArray');

		$return = $dsn->toArray();
		$expected = [
			'scheme' => 'mysql',
			'host' => 'somewhereelse',
			'port' => 3306,
			'user' => 'user',
			'pass' => 'password',
			'path' => '/database_name',
			'special' => 'nazrat'
		];
		$this->assertSame($expected, $return, 'a bespoke getter should be called by toArray');

		$this->assertSame('nazrat', $dsn->special, 'Special property should return the stored value');

		$url = $dsn->toUrl();
		$expected = 'mysql://user:password@somewhereelse:3306/database_name?special=nazrat';
		$this->assertSame($expected, $url, 'Updated values should be reflected in the string version');
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
 * Returns an array of dsns which should parse back to the same string
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

/**
 * testParseNoClass
 *
 * Parsing a service with no specific dsn class should be an instance of the static class
 *
 * @return void
 */
	public function testParseNoClass() {
		$dsn = Dsn::parse('service://host/path');
		$this->assertInstanceOf('AD7six\Dsn\Dsn', $dsn);
	}

/**
 * testParseSpecificClass
 *
 * Parsing a service where a specific dsn class exists should return an instance of that class
 * even if, as is the case here, it's another pseudo-abstract class
 *
 * @return void
 */
	public function testParseSpecificClass() {
		$dsn = Dsn::parse('db://host/path');
		$this->assertInstanceOf('AD7six\Dsn\DbDsn', $dsn);
	}

}
