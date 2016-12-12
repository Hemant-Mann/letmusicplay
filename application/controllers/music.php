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
use Framework\ArrayMethods as ArrayMethods;

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
        $view = $this->getActionView();

        $q = RequestMethods::get("q");
        $this->seo(array("title" => $q));
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
    	$this->seo(array("title" => "Download " .$title. " video and mp3", "description" => "Download mp3 and Video of ". $title. " all quality for free and save in 240p, 360p, 720p, 1080p", "photo" => "http://letmusicplay.in/home/image/{$id}.jpg"));
        $view = $this->getActionView();

        if (!$id) $this->redirect("/");
        
        $qualities = Youtube::formats($id);
        $view->set("title", $title)
            ->set("formats", $qualities['video'])
            ->set("mp3", $qualities['audio'])
            ->set("id", $id);
    }

    public function convert($youtubeid = '') {
    	$this->seo(array("title" => "Music Search"));
        $view = $this->getActionView();
    }

    public function download($fmt = 18, $youtubeid = '') {
        $this->noview();

        $extension = RequestMethods::get("ext", "mp4");
        switch ($extension) {
            case 'mp4':
                $action = "video";
                break;
            
            case 'mp3':
                $action = "audio";
                break;
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
        $referer = RequestMethods::server("HTTP_REFERER", "");

        $title .= ".{$extension}";
        $headers = getallheaders();
        if (isset($headers['X-Requested-With'])) {
            echo "success";
        } else if (stristr($referer, "http://" . $_SERVER["HTTP_HOST"] . "/")) {
            $cmd = 'export PATH="/usr/local/bin:$PATH"; /usr/local/bin/node ' . APP_PATH . '/application/libraries/Music/index.js ' . $youtubeid;
            exec($cmd, $output, $return);
            $this->_update($youtubeid);

            // $this->_sendFile($file, $title);
            $this->_smartReadFile($file, basename($title));
        } else {
            $this->redirect("/404");
        }
    }

    protected function _sendFile($file, $title) {
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Cache-Control: private",false);
        header('Pragma: public');
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . "LetMusicPlay.in--" . basename($title) . '"');
        header("Content-Transfer-Encoding: binary");
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }

    protected function _smartReadFile($location, $filename, $mimeType='application/octet-stream') {
        if (!file_exists($location)) {
            header ("HTTP/1.0 404 Not Found");
            return;
        }
        // $finalLoc = "/Users/hemant/Downloads/test/downloads/{$filename}";
        // copy($location, $finalLoc);
        // $this->redirect("/");
      
        $size = filesize($location);
        $time = date('r', filemtime($location));

        $fm = @fopen($location,'rb');
        if (!$fm) {
            header ("HTTP/1.0 505 Internal server error");
            return;
        }

        $begin = 0;
        $end = $size;

        if (isset($_SERVER['HTTP_RANGE'])) { 
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) { 
                $begin = intval($matches[0]);

                if(!empty($matches[1])) {
                    $end = intval($matches[1]);
                }
            }
        }
      
        if ($begin > 0 || $end < $size)
            header('HTTP/1.0 206 Partial Content');
        else
            header('HTTP/1.0 200 OK');  

        header("Content-Type: $mimeType"); 
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: no-cache');  
        header('Accept-Ranges: bytes');
        header('Content-Length:'. ($end - $begin));
        header("Content-Range: bytes $begin-$end/$size");
        header('Content-Disposition: inline; filename="'.$filename.'"');
        header("Content-Transfer-Encoding: binary\n");
        header("Last-Modified: $time");
        header('Connection: close');  

        $cur = $begin;
        fseek($fm, $begin, 0);

        while(!feof($fm) && $cur < $end && (connection_status() == 0)) {
            print fread($fm, min(1024 * 16, $end - $cur));
            $cur += 1024 * 16;
        }
        exit;
    }

    protected function _update($youtubeid) {
        $r = Models\Download::first(['youtube_id = ?' => $youtubeid]);
        if (!$r) {
            $r = new Models\Download([
                'youtube_id' => $youtubeid
            ]);
        }
        $r->count++;
        $r->save();
    }

    public function trending() {
    	$this->seo(array("title" => "Music | Trending"));
        $view = $this->getActionView();

        $cursor = Models\Download::all([], ["youtube_id", "created"], "count", "desc", 50, 1);

        $curl = new \Curl\Curl(); $result = [];
        foreach ($cursor as $c) {
            // Awesome url gives info about video using VideoID :)
            $curl->get("https://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=". $c->youtube_id ."&format=json");
            $response = $curl->response;

            $result[] = ArrayMethods::toObject([
                'id' => $c->youtube_id,
                'title' => $response->title,
                'img' => '/home/image/' . $c->youtube_id . '.jpg'
            ]);
        }

        $view->set("songs", $result);
    }

    public function api() {
        $this->seo(array("title" => "API for mp3 and video downloads"));
        $view = $this->getActionView();
    }

}
