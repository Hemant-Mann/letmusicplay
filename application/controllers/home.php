<?php

/**
 * The Default Example Controller Class
 *
 * @author Faizan Ayubi, Hemant Mann
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\ArrayMethods as ArrayMethods;

class Home extends Controller {
	public function image($id = null) {
		$this->noview();
		header ("Cache-Control: max-age=6000");
		if (isset($id)) {
			$url = "http://img.youtube.com/vi/$id/hqdefault.jpg";
			$ctype = "image/jpeg";

			header('Content-type: ' . $ctype);
			echo file_get_contents($url);
		} else {
			header('Content-type: image/png');
			readfile(APP_PATH . "/public/assets/img/logo.png");
		}
	}

    public function index() {
    	$layoutView = $this->getLayoutView();
    	$layoutView->set("seo", Framework\Registry::get("seo"));
    	$view = $this->getActionView();

    	$collection = Registry::get("MongoDB")->downloads;
    	$before = strtotime("-10 day");
    	$cursor = $collection->find(array('created' => array(
    		'$gt' => new \MongoDate($before),
    		'$lte' => new \MongoDate()
    	)));
    	$cursor->limit(10);
    	$cursor->sort(array('count' => -1));

        $curl = new \Curl\Curl(); $result = [];
        foreach ($cursor as $c) {
            // Awesome url gives info about video using VideoID :)
            $curl->get("https://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=". $c['youtube_id']."&format=json");
            $response = $curl->response;

            $result[] = ArrayMethods::toObject([
                'youtube_id' => $c['youtube_id'],
                'title' => $response->title,
                'img' => '/home/image/' . $c['youtube_id'] . '.jpg'
            ]);
        }

    	$view->set("songs", $result);
    }

}
