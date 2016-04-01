<?php

/**
 * The Default Example Controller Class
 *
 * @author Faizan Ayubi, Hemant Mann
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;
use Shared\Services\Youtube as Youtube;

class Music extends Controller {
    /**
     * Initialized Youtube API Client
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        $configuration = Registry::get("configuration");
        $parsed = $configuration->parse("configuration/google");
        
        $client = new Google_Client();
        $client->setDeveloperKey($parsed->google->developer->key);
        $youtube = new Google_Service_YouTube($client);

        Registry::set("gClient", $client);
        Registry::set("youtube", $youtube);
    }

    public function search() {
        $this->seo(array("title" => "Music Search"));
        $view = $this->getActionView();

        $q = RequestMethods::get("q");
        $results = Youtube::search($q);
        if (is_string($results)) {
            $songs = array();
        } else {
            $songs = $results;
        }

        $view->set("songs", $songs)
            ->set("query", $q);
    }

    public function view($title = '', $id = null) {
    	$this->seo(array("title" => $title, "photo" => "http://letmusicplay.in/home/image/{$id}.jpg"));
        $view = $this->getActionView();

        if (!$id) $this->redirect("/");
        
        $formats = Youtube::formats($id);
        $view->set("title", $title)
            ->set("formats", $formats)
            ->set("id", $id);
    }

    public function convert($youtubeid = '') {
    	$this->seo(array("title" => "Music Search"));
        $view = $this->getActionView();
    }

    public function download($fmt = 18, $youtubeid = '') {
        $this->noview();

        $extension = RequestMethods::get("ext", "mp3");
        if (preg_match('/[a-z]/i', $fmt)) {
            $fmt = "mp3"; $action = "mp3";
        } else {
            $action = "video";
        }
        try {
            $file = Youtube::download($youtubeid, [
                'action' => $action,
                'fmt' => $fmt,
                'extension' => $extension
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage();
            return;
        }
        $title = RequestMethods::get("title", $file);
        $data = array(
            "title" => $title,
            "comment" => "letmusicplay.in"
        );
        $result = id3_set_tag($file, $data);
        $title .= ".{$extension}";
        $headers = getallheaders();
        if (isset($headers['X-Requested-With'])) {
            echo "success";
        } elseif (file_exists($file)) {
            $this->_update($youtubeid);
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header("Cache-Control: private",false);
            header('Pragma: public');
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . "LetMusicPlay.in" . basename($title) . '"');
            header("Content-Transfer-Encoding: binary");
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }

    protected function _update($youtubeid) {
        $downloads = Registry::get("MongoDB")->downloads;
        $record = $downloads->findOne(array('youtube_id' => $youtubeid));
        if (!isset($record)) {
            $downloads->insert([
                'youtube_id' => $youtubeid,
                'created' => new \MongoDate(),
                'count' => 1
            ]);
        } else {
            $count = $record['count'];
            $downloads->update(['youtube_id' => $youtubeid], ['$set' => ['count' => (int) $count + 1]]);
        }
    }

    public function trending() {
    	$this->seo(array("title" => "Music Search"));
        $view = $this->getActionView();
    }

}
