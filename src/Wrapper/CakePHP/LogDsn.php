<?php

namespace AD7six\Dsn\Wrapper\CakePHP;

use AD7six\Dsn\Wrapper\Dsn;

class LogDsn extends Dsn {

	public static function parse($url, $options = []) {
		$keyMap = [
			'scheme' => 'engine'
		];
		if (!isset($options['keyMap'])) {
			$options['keyMap'] = [];
		}
		$options['keyMap'] += $keyMap;

		$getters = [
			'engine' => function($x, $dsn) {
				return ucfirst($dsn->scheme);
			}
		];
		if (!isset($options['getters'])) {
			$options['getters'] = [];
		}
		$options['getters'] += $getters;

		$replacements = [
			'/LOGS/' => LOGS,
		];
		if (!isset($options['replacements'])) {
			$options['replacements'] = [];
		}
		$options['replacements'] += $replacements;

		return (new Dsn($url, $options))->toArray();
	}
}
