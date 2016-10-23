<?php

/**
 * @author Hemant Mann
 */
class Cron extends \Framework\Controller {

	public function __construct($options = array()) {
        parent::__construct($options);
        $this->willRenderLayoutView = false;
        $this->willRenderActionView = false;

        if (php_sapi_name() != 'cli') {
            $this->_404();
        }
    }

	public function index($type = "daily") {
		switch ($type) {
            case 'hourly':
                $this->_hourly();
                break;

            case 'daily':
                $this->_daily();
                break;

            case 'weekly':
                $this->_weekly();
                break;

            case 'monthly':
                $this->_monthly();
                break;
        }
	}

	protected function _daily() {
		$this->_log("Cron Started!!");
		$this->_removeFiles();
		$this->_log("Cron End!!");

	}

	protected function _hourly() {}
	protected function _weekly() {}
	protected function _monthly() {}


	protected function _removeFiles() {
		$path = APP_PATH . "/application/libraries/YTDownloader/downloads/";
		$cmd = "find $path -mtime +2 -exec rm '{}' +";
		exec($cmd, $output, $return);

		if ($return === 0) {
			$this->_log("Unused Files Removed");
		} else {
			$this->_log("***** Failed to remove files ******");
		}
	}

	protected function _log($message = "") {
        $logfile = APP_PATH . "/logs/" . date("Y-m-d") . ".txt";
        $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
        $content = "[{$timestamp}] {$message}\n";
        
        file_put_contents($logfile, $content, FILE_APPEND);
    }
}
