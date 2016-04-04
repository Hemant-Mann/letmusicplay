<?php

/**
 * @author Hemant Mann
 */
class Cron extends \Framework\Controller {
	/**
	 * @before _secure
	 */
	public function index() {
		$this->_log("Cron JOB Started");
		$this->_removeFiles();
	}

	protected function _removeFiles() {
		$path = APP_PATH . "/application/libraries/YTDownloader/downloads/*";
		$cmd = "find $path -mtime +30 -exec rm {} \;"
		exec($cmd, $output, $return);

		if ($return === 0) {
			$this->_log("Unused Files Removed");
		} else {
			$this->_log("***** Failed to remove files ******");
		}
	}

	/**
	 * @protected
	 */
	public function _secure() {
		if (php_sapi_name() !== 'cli') {
            $this->redirect("/404");
        }
	}

	protected function _log($message = "") {
        $logfile = APP_PATH . "/logs/" . date("Y-m-d") . ".txt";
        $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
        $content = "[{$timestamp}] {$message}\n";
        
        file_put_contents($logfile, $content, FILE_APPEND);
    }
}
