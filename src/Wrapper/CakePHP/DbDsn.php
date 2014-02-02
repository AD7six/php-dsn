<?php

namespace AD7six\Dsn\Wrapper\CakePHP;

use AD7six\Dsn\Wrapper\Dsn;

class DbDsn extends Dsn {

	protected static $_defaultOptions = [
		'keyMap' => [
			'engine' => 'datasource',
			'user' => 'login',
			'pass' => 'password'
		]
	];

	public static function parse($url, $options = []) {
		$inst = new DbDsn($url, $options);
		return $inst->toArray();
	}

	public function getDatasource() {
		return 'Database/' . ucfirst($this->_dsn->engine);
	}

	public function setDatasource($value) {
		$this->_dsn->engine = str_replace('Database/', '', $value);
	}

}
