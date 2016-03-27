<?php

/**
 * The Default Example Controller Class
 *
 * @author Faizan Ayubi, Hemant Mann
 */
use Shared\Controller as Controller;

class Music extends Controller {

    public function search() {
       $this->seo(array("title" => "Music Search", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
    }

    public function view($title='', $id='') {
    	$this->seo(array("title" => "Music Search", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
    }

    public function convert($youtubeid='') {
    	$this->seo(array("title" => "Music Search", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
    }

    public function download($youtubeid='') {
    	$this->seo(array("title" => "Music Search", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
    }

    public function trending() {
    	$this->seo(array("title" => "Music Search", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
    }

}
