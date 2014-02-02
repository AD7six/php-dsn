<?php

namespace AD7six\Dsn\Wrapper\CakePHP;

use AD7six\Dsn\Wrapper\Dsn;

class LogDsn extends Dsn {

	protected static $_defaultOptions = [
		'keyMap' => [
			'scheme' => 'engine'
		],
		'replacements' => [
			'/LOGS/' => LOGS
		]
	];

	public function getEngine() {
		return ucfirst($this->_dsn->scheme);
	}

	public function setEngine($value) {
		$this->_dsn->scheme = lcfirst($value);
	}
}
