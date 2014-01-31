<?php

namespace AD7six\Dsn\Wrapper\CakePHP;

use AD7six\Dsn\Wrapper\Dsn;

class CacheDsn extends Dsn {

	public static function parse($url, $options) {
		$keyMap = [
			'scheme' => 'engine'
		];

		$replacements = [
			'/CACHE/' => CACHE,
			'DURATION' =>  \Configure::read('debug') ? '+10 seconds' : '+999 days'
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
