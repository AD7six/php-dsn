<?php

namespace AD7six\Dsn\TestCase;

use \AD7six\Dsn\DbDsn;
use \PHPUnit_Framework_TestCase;

/**
 * DbDsnTest
 *
 */
class DbDsnTest extends PHPUnit_Framework_TestCase {

	public function testDefaultPorts() {
		$url = 'mysql://user:password@localhost/database_name';
		$dsn = new DbDsn($url);
		$dsn->defaultPort(3306);

		$expected = [
			'engine' => 'mysql',
			'host' => 'localhost',
			'port' => 3306,
			'user' => 'user',
			'pass' => 'password',
			'database' => 'database_name',
		];

		$return = $dsn->toArray();
		$this->assertSame($expected, $return, 'Default port should be in the parsed result');

		$this->assertSame($url, (string) $dsn, 'The regenerated url should be identical to the input');
	}

/**
 * testParseNoClass
 *
 * Parsing a service with no specific dsn class should be an instance of the static class
 *
 * @return void
 */
	public function testParseNoClass() {
		$dsn = DbDsn::parse('service://host/path');
		$this->assertInstanceOf('AD7six\Dsn\DbDsn', $dsn);
	}

/**
 * testParseMysql
 *
 * @return void
 */
	public function testParseMysql() {
		$url = 'mysql://user:password@localhost/database_name';
		$dsn = DbDsn::parse($url);
		$this->assertInstanceOf('AD7six\Dsn\MysqlDsn', $dsn);

		$expected = [
			'engine' => 'mysql',
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
		$dsn = DbDsn::parse($url);
		$this->assertInstanceOf('AD7six\Dsn\SqliteDsn', $dsn);

		$expected = [
			'engine' => 'sqlite',
			'database' => '/over/here.db',
		];

		$return = $dsn->toArray();
		$this->assertSame($expected, $return, 'Default port should be in the parsed result');

		$this->assertSame($url, (string)$dsn, 'The regenerated dsn should be the same as the input');
	}
}
