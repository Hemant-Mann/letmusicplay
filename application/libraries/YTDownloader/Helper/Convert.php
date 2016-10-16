<?php

namespace YTDownloader\Helper;
use YTDownloader\Exceptions\Argument;

class Convert {
	private static $_supportedFormats = array(
		'audio' => array(
			'mp2', 'mp3', '3gp'
		),
		'video' => array(
			'avi', 'flv'
		)
	);

	private function __construct() {
		// do nothing
	}

	private function __clone() {
		// do nothing
	}

	public static function To($extension, $inFile, $outFile) {
		if (in_array($extension, self::$_supportedFormats['audio']) || in_array($extension, self::$_supportedFormats['video'])) {
			$cmd = "ffmpeg -i {$inFile} -vn -ab 256k -ar 44100 -y {$outFile}";
			exec($cmd, $output, $return);
			if ($return !== 0) {
				throw new \YTDownloader\Exceptions\Core("Unable to convert the file");
			}
		} else {
			throw new Argument('Unsupported $format argument');
		}
	}
}
