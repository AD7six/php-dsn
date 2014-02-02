<?php

namespace AD7six\Dsn\Test\TestCase\Wrapper;

use \AD7six\Dsn\Wrapper\Dsn;
use \PHPUnit_Framework_TestCase;

/**
 * DsnTest
 *
 */
class DsnTest extends PHPUnit_Framework_TestCase {

/**
 * testBasic
 *
 * @return void
 */
	public function testBasic() {
		$url = 'service://host/path';
		$dsn = new Dsn($url);
		$this->assertInstanceOf('AD7six\Dsn\Wrapper\Dsn', $dsn);

		$this->assertSame('service', $dsn->scheme);
		$this->assertSame('service', $dsn->getScheme());
		$this->assertSame($url, $dsn->toUrl());

		$instance = $dsn->getDsn();
		$this->assertInstanceOf('AD7six\Dsn\Dsn', $instance);

		$dsn->scheme = 'foo';
		$this->assertSame('foo://host/path', $dsn->toUrl());
	}

}
