<?php

namespace AD7six\Dsn\Wrapper\CakePHP;

use AD7six\Dsn\Wrapper\Dsn;

class LogDsn extends Dsn {

	public static function parse($url, $options) {
		$keyMap = [
			'scheme' => 'engine'
		];

		$replacements = [
			'/LOGS/' => LOGS,
		];

		if (!isset($options['keyMap'])) {
			$options['keyMap'] = [];
		}

		if (!isset($options['replacements'])) {
			$options['replacements'] = [];
		}

		$options['keyMap'] += $keyMap;
		$options['replacements'] += $replacements;

		return (new Dsn($url, $options))->toArray();
	}
}
