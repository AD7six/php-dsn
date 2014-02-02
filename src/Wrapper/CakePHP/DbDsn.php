<?php

namespace AD7six\Dsn\Wrapper\CakePHP;

use AD7six\Dsn\Wrapper\Dsn;

class DbDsn extends Dsn {

	public static function parse($url, $options = []) {
		$keyMap = [
			'engine' => 'datasource',
			'user' => 'login',
			'pass' => 'password'
		];
		if (!isset($options['keyMap'])) {
			$options['keyMap'] = [];
		}
		$options['keyMap'] += $keyMap;

		$getters = [
			'datasource' => function($x, $dsn) {
				return 'Database/' . ucfirst($dsn->engine);
			}
		];
		if (!isset($options['getters'])) {
			$options['getters'] = [];
		}
		$options['getters'] += $getters;

		return (new Dsn($url, $options))->toArray();
	}

}
