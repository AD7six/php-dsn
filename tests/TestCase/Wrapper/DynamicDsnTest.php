<?php

namespace AD7six\Dsn\Test\TestCase\Wrapper;

use \AD7six\Dsn\Wrapper\DynamicDsn as Dsn;
use \PHPUnit_Framework_TestCase;

/**
 * DsnTest
 *
 */
class DynamicDsnTest extends PHPUnit_Framework_TestCase {

	public function testAccessor() {
		$url = 'service://host/path';
		$dsn = new Dsn($url);
		$this->assertInstanceOf('AD7six\Dsn\Wrapper\Dsn', $dsn);

		$dsn->addGetter('scheme', function($val, $dsn) { return strtoupper($dsn->scheme); });

		$this->assertSame('SERVICE', $dsn->scheme);
		$this->assertSame('SERVICE', $dsn->getScheme());
		$this->assertSame('service://host/path', $dsn->toUrl());
	}

	public function testModifierReturn() {
		$url = 'service://host/path';
		$dsn = new Dsn($url);

		$dsn->addSetter('scheme', function($val) { return 'something' . $val; });

		$dsn->scheme = 'else';

		$this->assertSame('somethingelse', $dsn->scheme);
		$this->assertSame('somethingelse', $dsn->getScheme());
		$this->assertSame('somethingelse://host/path', $dsn->toUrl());
	}

	public function testModifierDo() {
		$url = 'service://host/path';
		$dsn = new Dsn($url);

		$dsn->addSetter('scheme', function($val, $key, $dsn) { $dsn->$key = 42; });

		$dsn->scheme = 'not 42';

		$this->assertSame(42, $dsn->scheme);
		$this->assertSame(42, $dsn->getScheme());
		$this->assertSame('42://host/path', $dsn->toUrl());
	}
}
