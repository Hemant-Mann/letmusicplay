<?php

namespace YTDownloader\Service;

use YTDownloader\Exceptions\YTDL as YTDL;
use YTDownloader\Helper\Video as Video;
use YTDownloader\Helper\Convert as Convert;
use YTDownloader\Helper\Regex as Regex;

/**
 * This class will download youtube video
 * @param string $url Youtube Video URL
 */
class Download {
	/**
	 * Stores the Youtube URL
	 * @var string
	 */
	private $_url;

	/**
	 * Stores the Youtube Video ID
	 * @var string
	 */
	private $_videoId;

	/**
	 * Stores different video formats
	 * @var array
	 */
	private $_formats = [];

	/**
	 * Downloaded Video File Name
	 * @var string
	 */
	private $_file;

	/**
	 * Stores the default download location
	 * @var string
	 */
	private static $_root = null;

	public $bestMp3 = -1;

	public function __construct($url) {
		$id = Video::getId($url);
		$url = "https://www.youtube.com/watch?v="; // manually fix the url to prevent shell injection
		
		if ($id === false) {
			throw new YTDL("Invalid Youtube ID");
		}

		if (!self::$_root) {
			self::getDownloadPath();
		}
		$this->_url = $url . $id;
		$this->_videoId = $id;
	}

	protected function _download($code = 18, $extension = "mp4") {
		$fileName = $this->_videoId . "-{$code}" . ".{$extension}";
		$file = self::$_root . $fileName;

		if (!file_exists($file)) {
			$cmd = "/usr/local/bin/youtube-dl --no-warnings -f {$code} -o $file --max-filesize 300M " . $this->_url;
			exec($cmd, $output, $return);

			if ($return != 0) {
				throw new YTDL("Can't download video");
			}

			$output_string = implode(" ", $output);
			if (preg_match("/aborting/i", $output_string)) {
				throw new YTDL("File size exceeds the limit", 1);
			}
		}
		$this->_file = $file;
		return $fileName;
	}

	/**
	 * Executes the shell command for finding available video formats and
	 * parses the result using regular expression
	 */
	protected function _availableQualities() {
		$cmd = "/usr/local/bin/youtube-dl -F --no-warnings ". $this->_url;
		exec($cmd, $output, $return);
		
		if ($return != 0) {
			throw new YTDL("Can't get available video formats");
		}

		foreach ($output as $key => $value) {
			if ($key < 5) continue;

			$code = Video::getCode($value);
			if (preg_match("/(DASH\s(audio))/", $value, $match)) {
				$code = Video::getCode($value);
				$this->bestMp3 = Video::compare($code, $this->bestMp3);
				continue;
			}

			preg_match("/x([0-9]{3,4})/", $value, $match);

			if (!isset($match[1])) continue;

			if (!preg_match("/(DASH\s(video|audio))|only/", $value)) {
				preg_match("/^[0-9]{0,3}\s*(\w+)/", $value, $f);
				$this->_formats[$match[1]][$f[1]] = $code;	
			}
		}
		if ($this->bestMp3 === -1) {
			$this->bestMp3 = 140;
		}
	}

	/**
	 * Converts the video to given format
	 */
	public function convert($extension = "mp3", $code = 18) {
		Regex::validate(array('extension' => $extension));
		$filename = $this->_videoId . ".{$extension}";
		$this->_converted = self::$_root . $filename;
		
		if (!file_exists($this->_converted)) {
			$this->_download($code);
			Convert::To($extension, $this->_file, $this->_converted);
			@unlink($this->_file);
		}
		return $filename;
	}

	public function getVideoId() {
		return $this->_videoId;
	}

	public static function setDownloadPath($path) {
		self::$_root = basename($path);
	}

	public static function getDownloadPath() {
		if (!isset(self::$_root)) {
			self::$_root = dirname(dirname(__FILE__)) . "/downloads/";
		}
		return self::$_root;
	}

	public function getFile() {
		return $this->_converted;
	}

	/**
	 * @return array Returns an array of available qualities
	 */
	public function availableQualities() {
		$this->_availableQualities();
		$return = array();
		foreach ($this->_formats as $key => $value) {
			$return[$key."p"] = $value;
		}
		return $return;
	}

	/**
	 * downloads a video of given quality
	 * @param int $code Youtube Video code
	 * @param string $extension Video extension
	 * @return string Returns the name of the downloaded file
	 */
	public function download($code, $extension) {
		Regex::validate(array(
			'videoCode' => $code,
			'extension' => $extension
		));
		return $this->_download($code, $extension);
	}
}
