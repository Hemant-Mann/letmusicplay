<?php

/**
 * The Default Example Controller Class
 *
 * @author Faizan Ayubi, Hemant Mann
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\ArrayMethods as ArrayMethods;
use Models\Download;

class Home extends Controller {

	public function image($id = null, $type = "hqdefault") {
		$this->noview();
		header ("Cache-Control: max-age=6000");
		if (isset($id)) {
			$url = "http://img.youtube.com/vi/$id/{$type}.jpg";
			$ctype = "image/jpeg";

			header('Content-type: ' . $ctype);
			echo file_get_contents($url);
		} else {
			header('Content-type: image/png');
			readfile(APP_PATH . "/public/assets/img/logo.png");
		}
	}

    protected function _seo() {
        $layoutView = $this->getLayoutView();
        $layoutView->set("seo", Framework\Registry::get("seo"));
    }

    public function index() {
    	$this->_seo(); $view = $this->getActionView();
        $yesterday = date('Y-m-d H:i:s', strtotime("-1 day"));
        $today = date('Y-m-d H:i:s');

        $downloads = Download::all([
            "created between '$yesterday' and ?" => $today
        ], ["*"], "count", "desc", 10, 1);

        $curl = new \Curl\Curl(); $result = [];
        foreach ($downloads as $c) {
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

    public function dmca() {
        $this->_seo();
    }

}
